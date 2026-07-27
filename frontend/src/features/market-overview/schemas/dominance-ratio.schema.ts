import { z } from "zod";

export const dominanceRatioItemSchema = z.object({
  date: z.string(),
  stock_code: z.string(),
  institution: z.number(),
  retail: z.number(),
  mixed: z.number(),
  total_ratio: z.number(),
});

export const dominanceRatioResponseSchema = z.object({
  items: z.array(dominanceRatioItemSchema),
  meta: z.object({
    ratio_basis: z.literal("transaction_value"),
    unit: z.literal("percent"),
  }),
});

export type DominanceRatioResponseSchema = z.infer<
  typeof dominanceRatioResponseSchema
>;
