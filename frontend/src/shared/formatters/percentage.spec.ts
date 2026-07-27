import { describe, expect, it } from "vitest";
import { formatPercentRatio } from "./percentage";

describe("formatPercentRatio", () => {
  it("does not multiply 0-100 values again", () => {
    expect(formatPercentRatio(52.5)).toContain("52");
    expect(formatPercentRatio(52.5)).toContain("%");
    expect(formatPercentRatio(100)).toContain("100");
  });
});
