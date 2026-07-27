export const CHART_PALETTE = {
  positive: "#10B981",
  negative: "#EF4444",
  accumulation: "#059669",
  distribution: "#dc2626",
  foreign: "#059669",
  domestic: "#14B8A6",
  institution: "#047857",
  retail: "#14B8A6",
  mixed: "#F59E0B",
  zeroLine: "#D1D5DB",
  axis: "#6B7280",
  text: "#111827",
  textMuted: "#6B7280",
  grid: "#E5E7EB",
  background: "transparent",
  tooltipBg: "#030712",
} as const;

export const DOMINANCE_RATIO_TOLERANCE = { min: 99.5, max: 100.5 } as const;
