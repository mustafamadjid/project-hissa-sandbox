import { format, isValid, parse } from "date-fns";
import { id } from "date-fns/locale";

const API_DATE_PATTERN = "yyyy-MM-dd";

/**
 * Parse YYYY-MM-DD without timezone shift.
 */
export function parseApiDate(value: string): Date | null {
  const parsed = parse(value, API_DATE_PATTERN, new Date());
  return isValid(parsed) ? parsed : null;
}

export function formatApiDate(value: string): string {
  const parsed = parseApiDate(value);
  if (!parsed) {
    return value;
  }
  return format(parsed, "d MMM yyyy", { locale: id });
}

export function formatApiDateShort(value: string): string {
  const parsed = parseApiDate(value);
  if (!parsed) {
    return value;
  }
  return format(parsed, "d MMM", { locale: id });
}

export function isValidApiDate(value: string): boolean {
  return parseApiDate(value) !== null;
}

export function isValidDateRange(startDate: string, endDate: string): boolean {
  if (!isValidApiDate(startDate) || !isValidApiDate(endDate)) {
    return false;
  }
  return startDate <= endDate;
}
