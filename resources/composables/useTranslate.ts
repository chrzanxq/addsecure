import pl from '../locales/pl.json'

/**
 * Translation function, hardcoded to Polish.
 * Usage: const { t } = useTranslate(); t('vehicle.add')
 */
export function useTranslate() {
  const $t = (key: string): string => {
    return pl[key] || key
  }

  return { $t }
}
