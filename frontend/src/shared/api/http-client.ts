import axios, { type AxiosError, type AxiosInstance } from "axios";
import type { ApiError } from "@/shared/types/api.types";

const baseURL =
  import.meta.env.VITE_API_BASE_URL ?? "http://localhost:8000/api/v1";

export const httpClient: AxiosInstance = axios.create({
  baseURL,
  timeout: 30_000,
  headers: {
    Accept: "application/json",
    "Content-Type": "application/json",
  },
});

function extractMessage(data: unknown, fallback: string): string {
  if (data && typeof data === "object" && "message" in data) {
    const message = (data as { message?: unknown }).message;
    if (typeof message === "string" && message.length > 0) {
      return message;
    }
  }
  return fallback;
}

function extractValidationErrors(
  data: unknown,
): Record<string, string[]> | undefined {
  if (!data || typeof data !== "object" || !("errors" in data)) {
    return undefined;
  }
  const errors = (data as { errors?: unknown }).errors;
  if (!errors || typeof errors !== "object") {
    return undefined;
  }

  const result: Record<string, string[]> = {};
  for (const [key, value] of Object.entries(errors)) {
    if (Array.isArray(value) && value.every((item) => typeof item === "string")) {
      result[key] = value;
    }
  }
  return Object.keys(result).length > 0 ? result : undefined;
}

export function toApiError(error: unknown): ApiError {
  if (axios.isAxiosError(error)) {
    const axiosError = error as AxiosError<unknown>;
    const status = axiosError.response?.status ?? null;
    const data = axiosError.response?.data;

    if (axiosError.code === "ECONNABORTED") {
      return {
        status,
        code: "TIMEOUT",
        message: "Permintaan melebihi batas waktu. Coba lagi.",
        cause: error,
      };
    }

    if (!axiosError.response) {
      return {
        status: null,
        code: "NETWORK",
        message: "Tidak dapat terhubung ke server. Periksa koneksi Anda.",
        cause: error,
      };
    }

    if (status === 422) {
      const validationErrors = extractValidationErrors(data);
      return {
        status,
        code: "VALIDATION",
        message: extractMessage(data, "Parameter permintaan tidak valid."),
        ...(validationErrors ? { validationErrors } : {}),
        cause: error,
      };
    }

    if (status === 429) {
      return {
        status,
        code: "RATE_LIMIT",
        message: extractMessage(data, "Terlalu banyak permintaan. Coba lagi nanti."),
        cause: error,
      };
    }

    if (status !== null && status >= 500) {
      return {
        status,
        code: "SERVER",
        message: extractMessage(data, "Terjadi kesalahan pada server."),
        cause: error,
      };
    }

    return {
      status,
      code: "HTTP",
      message: extractMessage(data, "Permintaan gagal diproses."),
      cause: error,
    };
  }

  if (error instanceof Error) {
    return {
      status: null,
      code: "UNKNOWN",
      message: error.message || "Terjadi kesalahan tak terduga.",
      cause: error,
    };
  }

  return {
    status: null,
    code: "UNKNOWN",
    message: "Terjadi kesalahan tak terduga.",
    cause: error,
  };
}

export function getApiErrorMessage(error: unknown): string {
  return toApiError(error).message;
}

/** Build axios config without writing undefined into optional keys. */
export function withRequestSignal(
  signal?: AbortSignal,
): { signal: AbortSignal } | Record<string, never> {
  return signal ? { signal } : {};
}
