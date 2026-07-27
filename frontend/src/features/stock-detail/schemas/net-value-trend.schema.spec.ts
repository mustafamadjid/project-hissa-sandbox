import { describe, expect, it } from "vitest";
import { netValueTrendResponseSchema } from "./net-value-trend.schema";

describe("netValueTrendResponseSchema", () => {
  it("parses valid trend response", () => {
    const result = netValueTrendResponseSchema.parse({
      stock_code: "BBCA",
      period: { start_date: "2026-07-01", end_date: "2026-07-03" },
      points: [
        {
          date: "2026-07-01",
          stock_code: "BBCA",
          net_value: 1250000000,
          classification: "accumulation",
        },
      ],
      meta: { unit: "IDR" },
    });

    expect(result.points[0]?.net_value).toBe(1250000000);
  });
});
