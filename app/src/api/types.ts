export type SuccessResponse<T> = [null, T]

export type ErrorResponse = [Error, null]

export type ApiResponse<T> = SuccessResponse<T> | ErrorResponse
