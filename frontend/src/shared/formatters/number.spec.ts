import { describe, expect, it } from "vitest";
import { formatAxisIdr, formatCompactNumber, formatNumber } from "./number";

describe("formatAxisIdr", () => {
  it("uses triliun scale", () => {
    expect(formatAxisIdr(2_500_000_000_000)).toBe("2.5T");
  });

  it("uses miliar scale", () => {
    expect(formatAxisIdr(1_250_000_000)).toBe("1.3M");
  });

  it("uses juta scale", () => {
    expect(formatAxisIdr(2_500_000)).toBe("2.5jt");
  });

  it("uses ribu scale", () => {
    expect(formatAxisIdr(12_500)).toBe("12.5rb");
  });

  it("keeps sign for negative values", () => {
    expect(formatAxisIdr(-1_000_000_000)).toBe("-1.0M");
  });
});

describe("formatCompactNumber", () => {
  it("returns compact locale string", () => {
    expect(formatCompactNumber(1_500_000).length).toBeGreaterThan(0);
  });
});

describe("formatNumber", () => {
  it("formats plain integers", () => {
    expect(formatNumber(1000)).toMatch(/1/);
  });
});
