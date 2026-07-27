import { describe, expect, it } from "vitest";
import {
  formatApiDate,
  isValidApiDate,
  isValidDateRange,
  parseApiDate,
} from "./date";

describe("parseApiDate", () => {
  it("parses YYYY-MM-DD without timezone shift", () => {
    const parsed = parseApiDate("2026-07-01");
    expect(parsed).not.toBeNull();
    expect(parsed!.getFullYear()).toBe(2026);
    expect(parsed!.getMonth()).toBe(6);
    expect(parsed!.getDate()).toBe(1);
  });

  it("returns null for invalid date", () => {
    expect(parseApiDate("not-a-date")).toBeNull();
    expect(parseApiDate("2026-13-40")).toBeNull();
  });
});

describe("formatApiDate", () => {
  it("formats valid date for UI", () => {
    const result = formatApiDate("2026-07-01");
    expect(result).toContain("2026");
    expect(result).not.toBe("2026-07-01");
  });

  it("returns original string when invalid", () => {
    expect(formatApiDate("bad")).toBe("bad");
  });
});

describe("isValidDateRange", () => {
  it("accepts equal and ordered ranges", () => {
    expect(isValidDateRange("2026-07-01", "2026-07-01")).toBe(true);
    expect(isValidDateRange("2026-07-01", "2026-07-22")).toBe(true);
  });

  it("rejects inverted or invalid ranges", () => {
    expect(isValidDateRange("2026-07-22", "2026-07-01")).toBe(false);
    expect(isValidDateRange("bad", "2026-07-01")).toBe(false);
  });
});

describe("isValidApiDate", () => {
  it("validates format", () => {
    expect(isValidApiDate("2026-07-01")).toBe(true);
    expect(isValidApiDate("01-07-2026")).toBe(false);
  });
});
