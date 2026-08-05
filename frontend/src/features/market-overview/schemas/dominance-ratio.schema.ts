import { z } from "zod";

export const dominanceRatioPointSchema = z.object({
  date: z.string(),
  institution_ratio: z.number(),
  retail_ratio: z.number(),
  mixed_ratio: z.number(),
  total_ratio: z.number(),
});

export const dominanceRatioResponseSchema = z.object({
  stock_code: z.string(),
  period: z.object({ start_date: z.string(), end_date: z.string() }),
  granularity: z.enum(["daily", "weekly", "monthly"]),
  points: z.array(dominanceRatioPointSchema),
  meta: z.object({
    ratio_basis: z.literal("transaction_value"),
    unit: z.literal("percent"),
    aggregation: z.enum(["daily", "latest"]),
    timezone: z.string(),
  }),
});

export type DominanceRatioResponseSchema = z.infer<
  typeof dominanceRatioResponseSchema
>;
