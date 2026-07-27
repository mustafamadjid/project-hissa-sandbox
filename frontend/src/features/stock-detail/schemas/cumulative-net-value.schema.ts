import { z } from "zod";

export const cumulativeNetValueResponseSchema = z.object({
  stock_code: z.string(),
  period: z.object({ start_date: z.string(), end_date: z.string() }),
  points: z.array(z.object({ date: z.string(), daily_net_value: z.number(), cumulative_net_value: z.number() })),
  meta: z.object({ reset_policy: z.literal("start_of_period"), unit: z.literal("IDR"), granularity: z.literal("daily") }),
});
