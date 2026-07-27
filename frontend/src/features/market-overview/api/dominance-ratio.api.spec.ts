import { beforeEach, describe, expect, it, vi } from "vitest";

vi.mock("@/shared/api/http-client", () => ({
  httpClient: {
    get: vi.fn(),
  },
  toApiError: (error: unknown) => error,
  withRequestSignal: () => ({}),
}));

import { httpClient } from "@/shared/api/http-client";
import { fetchDominanceRatio } from "./dominance-ratio.api";

describe("fetchDominanceRatio", () => {
  beforeEach(() => {
    vi.mocked(httpClient.get).mockReset();
  });

  it("omits empty stock_code from query", async () => {
    vi.mocked(httpClient.get).mockResolvedValue({
      data: {
        items: [],
        meta: { ratio_basis: "transaction_value", unit: "percent" },
      },
    });

    await fetchDominanceRatio({
      start_date: "2026-07-01",
      end_date: "2026-07-22",
      stock_code: "",
    });

    expect(httpClient.get).toHaveBeenCalledWith(
      "/market/dominance-ratio",
      expect.objectContaining({
        params: {
          start_date: "2026-07-01",
          end_date: "2026-07-22",
        },
      }),
    );
  });

  it("includes stock_code when provided", async () => {
    vi.mocked(httpClient.get).mockResolvedValue({
      data: {
        items: [],
        meta: { ratio_basis: "transaction_value", unit: "percent" },
      },
    });

    await fetchDominanceRatio({
      start_date: "2026-07-01",
      end_date: "2026-07-22",
      stock_code: "BBCA",
    });

    expect(httpClient.get).toHaveBeenCalledWith(
      "/market/dominance-ratio",
      expect.objectContaining({
        params: {
          start_date: "2026-07-01",
          end_date: "2026-07-22",
          stock_code: "BBCA",
        },
      }),
    );
  });
});
