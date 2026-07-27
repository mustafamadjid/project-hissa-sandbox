import { z } from "zod";

export const foreignFlowQuadrantSchema = z.enum([
  "foreign_buy_accumulation",
  "foreign_sell_distribution",
  "foreign_buy_distribution",
  "foreign_sell_accumulation",
]);

export const foreignFlowScatterResponseSchema = z.object({
  period: z.object({ start_date: z.string(), end_date: z.string() }),
  items: z.array(z.object({
    stock_code: z.string().min(1),
    foreign_net_flow: z.number(),
    net_value: z.number(),
    domestic_net_flow: z.number(),
    quadrant: foreignFlowQuadrantSchema,
  })),
  meta: z.object({ unit: z.literal("IDR"), aggregation: z.literal("sum") }),
});
