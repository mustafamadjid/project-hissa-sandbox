import { format, subDays } from "date-fns";

export function defaultEndDate(): string {
  return format(new Date(), "yyyy-MM-dd");
}

export function defaultStartDate(daysBack = 30): string {
  return format(subDays(new Date(), daysBack), "yyyy-MM-dd");
}
