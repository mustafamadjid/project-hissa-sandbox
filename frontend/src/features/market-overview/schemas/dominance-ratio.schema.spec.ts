import { describe, expect, it } from "vitest";
import { dominanceRatioResponseSchema } from "./dominance-ratio.schema";

describe("dominanceRatioResponseSchema", () => {
  it("parses valid dominance response", () => {
    const result = dominanceRatioResponseSchema.parse({
      items: [
        {
          date: "2026-07-01",
          stock_code: "BBCA",
          institution: 52.5,
          retail: 35,
          mixed: 12.5,
          total_ratio: 100,
        },
      ],
      meta: { ratio_basis: "transaction_value", unit: "percent" },
    });

    expect(result.items[0]?.total_ratio).toBe(100);
  });
});
