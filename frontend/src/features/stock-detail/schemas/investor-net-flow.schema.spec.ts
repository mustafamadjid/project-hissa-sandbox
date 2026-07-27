import { describe, expect, it } from "vitest";
import { investorNetFlowResponseSchema } from "./investor-net-flow.schema";

describe("investorNetFlowResponseSchema", () => {
  it("parses valid investor net flow response", () => {
    const result = investorNetFlowResponseSchema.parse({
      stock_code: "BBCA",
      points: [
        {
          date: "2026-07-01",
          foreign_net_flow: 900000000,
          domestic_net_flow: -900000000,
        },
      ],
      meta: { unit: "IDR", granularity: "daily" },
    });

    expect(result.points[0]?.foreign_net_flow).toBe(900000000);
  });
});
