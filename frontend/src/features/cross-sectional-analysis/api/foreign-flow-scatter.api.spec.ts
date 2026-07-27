import { beforeEach, describe, expect, it, vi } from "vitest";

vi.mock("@/shared/api/http-client", () => ({
  httpClient: { get: vi.fn() },
  toApiError: (error: unknown) => error,
  withRequestSignal: () => ({}),
}));

import { httpClient } from "@/shared/api/http-client";
import { fetchForeignFlowScatter } from "./foreign-flow-scatter.api";

describe("fetchForeignFlowScatter", () => {
  beforeEach(() => {
    vi.mocked(httpClient.get).mockReset();
  });

  it("sends snake_case query params", async () => {
    vi.mocked(httpClient.get).mockResolvedValue({
      data: {
        period: { start_date: "2026-07-01", end_date: "2026-07-22" },
        items: [],
        meta: { unit: "IDR", aggregation: "sum" },
      },
    });

    await fetchForeignFlowScatter({
      start_date: "2026-07-01",
      end_date: "2026-07-22",
      aggregation: "sum",
      stock_codes: "BBCA,BBRI",
      min_abs_value: 1_000_000_000,
      limit: 50,
    });

    expect(httpClient.get).toHaveBeenCalledWith(
      "/market/foreign-flow-net-value-scatter",
      expect.objectContaining({
        params: {
          start_date: "2026-07-01",
          end_date: "2026-07-22",
          aggregation: "sum",
          stock_codes: "BBCA,BBRI",
          min_abs_value: 1_000_000_000,
          limit: 50,
        },
      }),
    );
  });

  it("omits empty optional params", async () => {
    vi.mocked(httpClient.get).mockResolvedValue({
      data: {
        period: { start_date: "2026-07-01", end_date: "2026-07-22" },
        items: [],
        meta: { unit: "IDR", aggregation: "sum" },
      },
    });

    await fetchForeignFlowScatter({
      start_date: "2026-07-01",
      end_date: "2026-07-22",
      stock_codes: "",
    });

    expect(httpClient.get).toHaveBeenCalledWith(
      "/market/foreign-flow-net-value-scatter",
      expect.objectContaining({
        params: {
          start_date: "2026-07-01",
          end_date: "2026-07-22",
        },
      }),
    );
  });
});
