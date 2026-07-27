import { z } from "zod";

export const foreignGrossFlowResponseSchema = z.object({
  stock_code: z.string(),
  points: z.array(z.object({ date: z.string(), foreign_buy: z.number(), foreign_sell: z.number(), foreign_net_flow: z.number() })),
  meta: z.object({ unit: z.literal("IDR"), granularity: z.literal("daily") }),
});
