const compactFormatter = new Intl.NumberFormat("id-ID", {
  notation: "compact",
  compactDisplay: "short",
  maximumFractionDigits: 1,
});

const plainFormatter = new Intl.NumberFormat("id-ID", {
  maximumFractionDigits: 0,
});

export function formatCompactNumber(value: number): string {
  return compactFormatter.format(value);
}

export function formatNumber(value: number): string {
  return plainFormatter.format(value);
}

/** Axis labels for large IDR values: ribu / juta / miliar / triliun. */
export function formatAxisIdr(value: number): string {
  const abs = Math.abs(value);
  const sign = value < 0 ? "-" : "";

  if (abs >= 1_000_000_000_000) {
    return `${sign}${(abs / 1_000_000_000_000).toFixed(1)}T`;
  }
  if (abs >= 1_000_000_000) {
    return `${sign}${(abs / 1_000_000_000).toFixed(1)}M`;
  }
  if (abs >= 1_000_000) {
    return `${sign}${(abs / 1_000_000).toFixed(1)}jt`;
  }
  if (abs >= 1_000) {
    return `${sign}${(abs / 1_000).toFixed(1)}rb`;
  }
  return `${sign}${abs}`;
}
