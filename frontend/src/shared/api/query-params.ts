export type QueryPrimitive = string | number | boolean;
export type QueryValue = QueryPrimitive | null | undefined;

/**
 * Build a clean query object for Axios.
 * Drops undefined, null, and empty string values.
 */
export function sanitizeQueryParams(
  params: Record<string, QueryValue>,
): Record<string, QueryPrimitive> {
  const result: Record<string, QueryPrimitive> = {};

  for (const [key, value] of Object.entries(params)) {
    if (value === undefined || value === null) {
      continue;
    }
    if (typeof value === "string" && value.trim() === "") {
      continue;
    }
    result[key] = value;
  }

  return result;
}
