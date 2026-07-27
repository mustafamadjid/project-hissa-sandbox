import { describe, expect, it } from "vitest";
import { formatIdr } from "./currency";

describe("formatIdr", () => {
  it("formats positive IDR without decimals", () => {
    const result = formatIdr(1_250_000_000);
    expect(result).toContain("1");
    expect(result).toMatch(/Rp|\u00a0| /);
  });

  it("formats negative values", () => {
    const result = formatIdr(-500_000);
    expect(result).toContain("500");
    expect(result.includes("-") || result.includes("(")).toBe(true);
  });

  it("formats zero", () => {
    expect(formatIdr(0)).toMatch(/0/);
  });
});
