


export const formatDate = (date: Date, separator = '-', format: 'en' | 'es' = 'en') => {
  const day = date.getDate()
  const month = date.getMonth() + 1
  const year = date.getFullYear()

  if (format === 'es') {
    return `${day}${separator}${month}${separator}${year}`
  }

  return `${year}${separator}${month}${separator}${day}`
}
