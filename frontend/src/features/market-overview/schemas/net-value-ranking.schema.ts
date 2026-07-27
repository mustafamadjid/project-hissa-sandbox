import { z } from "zod";

export const netValueRankingItemSchema = z.object({
  rank: z.number().int().min(1),
  stock_code: z.string().min(1),
  net_value: z.number(),
  classification: z.enum(["accumulation", "distribution"]),
});

export const netValueRankingResponseSchema = z.object({
  period: z.object({
    start_date: z.string(),
    end_date: z.string(),
  }),
  items: z.array(netValueRankingItemSchema),
  meta: z.object({
    limit: z.number().int().min(1).max(100),
    aggregation: z.literal("sum"),
    unit: z.literal("IDR"),
  }),
});

export type NetValueRankingResponseSchema = z.infer<
  typeof netValueRankingResponseSchema
>;
