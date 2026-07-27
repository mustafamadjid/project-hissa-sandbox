import { z } from "zod";

export const netValueTrendPointSchema = z.object({
  date: z.string(),
  stock_code: z.string(),
  net_value: z.number(),
  classification: z.string(),
});

export const netValueTrendResponseSchema = z.object({
  stock_code: z.string(),
  period: z.object({
    start_date: z.string(),
    end_date: z.string(),
  }),
  points: z.array(netValueTrendPointSchema),
  meta: z.object({
    unit: z.literal("IDR"),
    updated_at: z.string().optional(),
  }),
});

export type NetValueTrendResponseSchema = z.infer<
  typeof netValueTrendResponseSchema
>;
