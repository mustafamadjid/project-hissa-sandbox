import { beforeEach, describe, expect, it, vi } from "vitest";

vi.mock("@/shared/api/http-client", () => ({
  httpClient: {
    get: vi.fn(),
  },
  toApiError: (error: unknown) => error,
  withRequestSignal: () => ({}),
}));

import { httpClient } from "@/shared/api/http-client";
import { fetchInvestorNetFlow } from "./investor-net-flow.api";

describe("fetchInvestorNetFlow", () => {
  beforeEach(() => {
    vi.mocked(httpClient.get).mockReset();
  });

  it("serializes path stock_code and query dates", async () => {
    vi.mocked(httpClient.get).mockResolvedValue({
      data: {
        stock_code: "BBRI",
        points: [],
        meta: { unit: "IDR", granularity: "daily" },
      },
    });

    await fetchInvestorNetFlow({
      stock_code: "BBRI",
      start_date: "2026-07-01",
      end_date: "2026-07-10",
    });

    expect(httpClient.get).toHaveBeenCalledWith(
      "/market/stocks/BBRI/investor/net-flow",
      expect.objectContaining({
        params: {
          start_date: "2026-07-01",
          end_date: "2026-07-10",
          granularity: "daily",
        },
      }),
    );
  });
});
