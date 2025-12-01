import { Link } from 'react-router-dom';
import SEO from '@/components/SEO';
import { useTranslation } from '@/hooks/useTranslation';

export default function NotFoundPage() {
  const { t } = useTranslation();
  return (
    <>
      <SEO
        title={`${t('error_404_title')} - ${t('error_404_heading')}`}
        description={t('error_404_desc')}
        url="/404"
        noindex={true}
        nofollow={true}
      />
      <div className="container-custom py-20">
      <div className="max-w-2xl mx-auto text-center">
        <h1 className="text-6xl font-bold text-primary-500 mb-4">{t('error_404_title')}</h1>
        <h2 className="text-3xl font-bold text-gray-900 mb-4">{t('error_404_heading')}</h2>
        <p className="text-lg text-gray-600 mb-8">
          {t('error_404_desc')}
        </p>
        <Link to="/" className="btn-primary">
          {t('error_return_home')}
        </Link>
      </div>
    </div>
    </>
  );
}

