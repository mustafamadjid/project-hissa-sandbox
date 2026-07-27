import { beforeEach, describe, expect, it, vi } from "vitest";

vi.mock("@/shared/api/http-client", () => ({
  httpClient: {
    get: vi.fn(),
  },
  toApiError: (error: unknown) => error,
  withRequestSignal: () => ({}),
}));

import { httpClient } from "@/shared/api/http-client";
import { fetchNetValueTrend } from "./net-value-trend.api";

describe("fetchNetValueTrend", () => {
  beforeEach(() => {
    vi.mocked(httpClient.get).mockReset();
  });

  it("puts stock code in path and dates in query", async () => {
    vi.mocked(httpClient.get).mockResolvedValue({
      data: {
        stock_code: "BBCA",
        period: { start_date: "2026-07-01", end_date: "2026-07-03" },
        points: [],
        meta: { unit: "IDR" },
      },
    });

    await fetchNetValueTrend({
      stock_code: "BBCA",
      start_date: "2026-07-01",
      end_date: "2026-07-03",
    });

    expect(httpClient.get).toHaveBeenCalledWith(
      "/tren-net-value/BBCA",
      expect.objectContaining({
        params: {
          start_date: "2026-07-01",
          end_date: "2026-07-03",
        },
      }),
    );
  });
});
