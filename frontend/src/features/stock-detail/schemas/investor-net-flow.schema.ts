import { z } from "zod";

export const investorNetFlowPointSchema = z.object({
  date: z.string(),
  foreign_net_flow: z.number(),
  domestic_net_flow: z.number(),
});

export const investorNetFlowResponseSchema = z.object({
  stock_code: z.string(),
  points: z.array(investorNetFlowPointSchema),
  meta: z.object({
    unit: z.literal("IDR"),
    granularity: z.literal("daily"),
  }),
});

export type InvestorNetFlowResponseSchema = z.infer<
  typeof investorNetFlowResponseSchema
>;
