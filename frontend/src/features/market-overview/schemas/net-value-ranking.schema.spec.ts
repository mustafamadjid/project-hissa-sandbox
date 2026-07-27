import { describe, expect, it } from "vitest";
import { netValueRankingResponseSchema } from "./net-value-ranking.schema";

describe("netValueRankingResponseSchema", () => {
  it("parses valid ranking response", () => {
    const result = netValueRankingResponseSchema.parse({
      period: { start_date: "2026-07-01", end_date: "2026-07-22" },
      items: [
        {
          rank: 1,
          stock_code: "GOTO",
          net_value: -3200000000,
          classification: "distribution",
        },
        {
          rank: 1,
          stock_code: "BBCA",
          net_value: 2500000000,
          classification: "accumulation",
        },
      ],
      meta: { limit: 10, aggregation: "sum", unit: "IDR" },
    });

    expect(result.items).toHaveLength(2);
    expect(result.meta.unit).toBe("IDR");
  });

  it("rejects invalid classification", () => {
    expect(() =>
      netValueRankingResponseSchema.parse({
        period: { start_date: "2026-07-01", end_date: "2026-07-22" },
        items: [
          {
            rank: 1,
            stock_code: "BBCA",
            net_value: 1,
            classification: "unknown",
          },
        ],
        meta: { limit: 10, aggregation: "sum", unit: "IDR" },
      }),
    ).toThrow();
  });
});
