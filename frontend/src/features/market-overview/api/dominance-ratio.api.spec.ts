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

  it("sends timeline filters", async () => {
    vi.mocked(httpClient.get).mockResolvedValue({
      data: {
        stock_code: "BBCA",
        period: { start_date: "2026-07-01", end_date: "2026-07-22" },
        granularity: "daily",
        points: [],
        meta: { ratio_basis: "transaction_value", unit: "percent", aggregation: "daily", timezone: "Asia/Jakarta" },
      },
    });

    await fetchDominanceRatio({
      start_date: "2026-07-01",
      end_date: "2026-07-22",
      stock_code: "BBCA",
      granularity: "daily",
    });

    expect(httpClient.get).toHaveBeenCalledWith(
      "/market/dominance-ratio",
      expect.objectContaining({
        params: {
          start_date: "2026-07-01",
          end_date: "2026-07-22",
          stock_code: "BBCA",
          granularity: "daily",
        },
      }),
    );
  });

  it("sends selected granularity", async () => {
    vi.mocked(httpClient.get).mockResolvedValue({
      data: {
        stock_code: "BBCA",
        period: { start_date: "2026-07-01", end_date: "2026-07-22" },
        granularity: "monthly",
        points: [],
        meta: { ratio_basis: "transaction_value", unit: "percent", aggregation: "latest", timezone: "Asia/Jakarta" },
      },
    });

    await fetchDominanceRatio({
      start_date: "2026-07-01",
      end_date: "2026-07-22",
      stock_code: "BBCA",
      granularity: "monthly",
    });

    expect(httpClient.get).toHaveBeenCalledWith(
      "/market/dominance-ratio",
      expect.objectContaining({
        params: {
          start_date: "2026-07-01",
          end_date: "2026-07-22",
          stock_code: "BBCA",
          granularity: "monthly",
        },
      }),
    );
  });
});
