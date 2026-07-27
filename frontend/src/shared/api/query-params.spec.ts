import { describe, expect, it } from "vitest";
import { sanitizeQueryParams } from "./query-params";

describe("sanitizeQueryParams", () => {
  it("keeps valid primitives", () => {
    expect(
      sanitizeQueryParams({
        start_date: "2026-07-01",
        end_date: "2026-07-22",
        limit: 10,
        active: true,
      }),
    ).toEqual({
      start_date: "2026-07-01",
      end_date: "2026-07-22",
      limit: 10,
      active: true,
    });
  });

  it("drops undefined, null, and empty string", () => {
    expect(
      sanitizeQueryParams({
        start_date: "2026-07-01",
        stock_code: undefined,
        limit: null,
        note: "",
        space: "   ",
      }),
    ).toEqual({
      start_date: "2026-07-01",
    });
  });

  it("preserves snake_case keys for backend contract", () => {
    const result = sanitizeQueryParams({
      start_date: "2026-07-01",
      end_date: "2026-07-03",
      stock_code: "BBCA",
    });
    expect(Object.keys(result)).toEqual([
      "start_date",
      "end_date",
      "stock_code",
    ]);
  });
});
