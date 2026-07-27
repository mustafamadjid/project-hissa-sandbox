import { describe, expect, it } from "vitest";
import { foreignFlowScatterResponseSchema } from "./foreign-flow-scatter.schema";

describe("foreignFlowScatterResponseSchema", () => {
  it("accepts valid scatter response", () => {
    const result = foreignFlowScatterResponseSchema.safeParse({
      period: { start_date: "2026-07-01", end_date: "2026-07-22" },
      items: [{
        stock_code: "BBCA",
        foreign_net_flow: 1_800_000_000,
        domestic_net_flow: -1_800_000_000,
        net_value: 2_500_000_000,
        quadrant: "foreign_buy_accumulation",
      }],
      meta: { unit: "IDR", aggregation: "sum" },
    });
    expect(result.success).toBe(true);
  });

  it("rejects unknown quadrant", () => {
    const result = foreignFlowScatterResponseSchema.safeParse({
      period: { start_date: "2026-07-01", end_date: "2026-07-22" },
      items: [{
        stock_code: "BBCA",
        foreign_net_flow: 1,
        domestic_net_flow: 1,
        net_value: 1,
        quadrant: "unknown",
      }],
      meta: { unit: "IDR", aggregation: "sum" },
    });
    expect(result.success).toBe(false);
  });
});
