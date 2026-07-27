export interface ApiError {
  status: number | null;
  code?: string;
  message: string;
  validationErrors?: Record<string, string[]>;
  cause?: unknown;
}

export interface DatePeriod {
  start_date: string;
  end_date: string;
}

export interface DateRangeParams {
  start_date: string;
  end_date: string;
}
