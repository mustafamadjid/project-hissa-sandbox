const STOCK_CODE_PATTERN = /^[A-Za-z0-9]{1,10}$/;

export function normalizeStockCode(value: string): string {
  return value.trim().toUpperCase();
}

export function isValidStockCode(value: string): boolean {
  return STOCK_CODE_PATTERN.test(value.trim());
}
