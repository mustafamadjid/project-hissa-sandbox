import { describe, expect, it } from "vitest";
import { dominanceRatioResponseSchema } from "./dominance-ratio.schema";

describe("dominanceRatioResponseSchema", () => {
  it("parses valid dominance response", () => {
    const result = dominanceRatioResponseSchema.parse({
      stock_code: "BBCA",
      period: { start_date: "2026-07-01", end_date: "2026-07-01" },
      granularity: "daily",
      points: [
        {
          date: "2026-07-01",
          institution_ratio: 52.5,
          retail_ratio: 35,
          mixed_ratio: 12.5,
          total_ratio: 100,
        },
      ],
      meta: {
        ratio_basis: "transaction_value",
        unit: "percent",
        aggregation: "daily",
        timezone: "Asia/Jakarta",
      },
    });

    expect(result.points[0]?.total_ratio).toBe(100);
  });
});
