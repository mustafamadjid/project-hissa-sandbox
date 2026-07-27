import { beforeEach, describe, expect, it, vi } from "vitest";

vi.mock("@/shared/api/http-client", () => ({
  httpClient: {
    get: vi.fn(),
  },
  toApiError: (error: unknown) => error,
  withRequestSignal: () => ({}),
}));

import { httpClient } from "@/shared/api/http-client";
import { fetchNetValueRanking } from "./net-value-ranking.api";

describe("fetchNetValueRanking", () => {
  beforeEach(() => {
    vi.mocked(httpClient.get).mockReset();
  });

  it("sends snake_case query params", async () => {
    vi.mocked(httpClient.get).mockResolvedValue({
      data: {
        period: { start_date: "2026-07-01", end_date: "2026-07-22" },
        items: [],
        meta: { limit: 10, aggregation: "sum", unit: "IDR" },
      },
    });

    await fetchNetValueRanking({
      start_date: "2026-07-01",
      end_date: "2026-07-22",
      limit: 10,
    });

    expect(httpClient.get).toHaveBeenCalledWith(
      "/market/net-value-ranking",
      expect.objectContaining({
        params: {
          start_date: "2026-07-01",
          end_date: "2026-07-22",
          limit: 10,
        },
      }),
    );
  });
});
