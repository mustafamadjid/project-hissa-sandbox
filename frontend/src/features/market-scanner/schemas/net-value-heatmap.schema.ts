import { z } from "zod";

export const netValueHeatmapResponseSchema = z.object({
  dates: z.array(z.string()),
  stocks: z.array(z.string()),
  cells: z.array(z.object({
    date: z.string(),
    stock_code: z.string(),
    net_value: z.number(),
    normalized_value: z.number(),
  })),
  meta: z.object({ color_min: z.number(), color_max: z.number() }),
});
