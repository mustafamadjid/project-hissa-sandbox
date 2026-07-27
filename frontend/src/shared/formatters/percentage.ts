const percentFormatter = new Intl.NumberFormat("id-ID", {
  style: "percent",
  minimumFractionDigits: 1,
  maximumFractionDigits: 1,
});

const ratioFormatter = new Intl.NumberFormat("id-ID", {
  minimumFractionDigits: 1,
  maximumFractionDigits: 1,
});

/**
 * Formats a value already in 0–100 percent range (does not multiply by 100).
 */
export function formatPercentRatio(value: number): string {
  return `${ratioFormatter.format(value)}%`;
}

/**
 * Formats a 0–1 fraction as percent.
 */
export function formatFractionAsPercent(value: number): string {
  return percentFormatter.format(value);
}
