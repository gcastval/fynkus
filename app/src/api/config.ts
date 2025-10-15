import { ENV } from '@/utils/const'
import type { ApiResponse } from './types'

type FetchOptions = {
  endpoint: string
  method?: 'GET' | 'POST' | 'PUT' | 'DELETE'
  body?: Record<string, unknown>
}

export const $fetch = async <T>({
  endpoint,
  method = 'GET',
  body,
}: FetchOptions): Promise<ApiResponse<T>> => {
  const fetchUrl = new URL(`${ENV.API_URL}/${endpoint}`)

  try {
    const res = await fetch(fetchUrl.toString(), {
      method,
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(body),
    }).then((response) => response.json())

    return [null, res]
  } catch (error) {
    return [error as Error, null]
  }
}
