import { Suspense, lazy } from 'react';
import { Routes, Route } from 'react-router-dom';
import Header from './components/Header';
import Footer from './components/Footer';
import ScrollToTop from './components/ScrollToTop';
import Loading from './components/Loading';

// Lazy load page components for better code splitting
const HomePage = lazy(() => import('./pages/HomePage'));
const ArticlesPage = lazy(() => import('./pages/ArticlesPage'));
const ArticleDetailPage = lazy(() => import('./pages/ArticleDetailPage'));
const StrategyCoursesPage = lazy(() => import('./pages/StrategyCoursesPage'));
const StrategyCourseDetailPage = lazy(() => import('./pages/StrategyCourseDetailPage'));
const ToolsPage = lazy(() => import('./pages/ToolsPage'));
const ToolDetailPage = lazy(() => import('./pages/ToolDetailPage'));
const AboutPage = lazy(() => import('./pages/AboutPage'));
const LoginPage = lazy(() => import('./pages/LoginPage'));
const NewsletterPage = lazy(() => import('./pages/NewsletterPage'));
const PrivacyPage = lazy(() => import('./pages/PrivacyPage'));
const NotFoundPage = lazy(() => import('./pages/NotFoundPage'));

function App() {
  return (
    <div className="min-h-screen flex flex-col">
      <ScrollToTop />
      <Header />
      <main className="flex-grow pt-20">
        <Suspense fallback={<Loading />}>
        <Routes>
          {/* Routes with optional language prefix */}
          <Route path="/" element={<HomePage />} />
          <Route path="/:lang" element={<HomePage />} />
          
          <Route path="/articles" element={<ArticlesPage />} />
          <Route path="/:lang/articles" element={<ArticlesPage />} />
          <Route path="/articles/:slug" element={<ArticleDetailPage />} />
          <Route path="/:lang/articles/:slug" element={<ArticleDetailPage />} />
          
          <Route path="/strategy-courses" element={<StrategyCoursesPage />} />
          <Route path="/:lang/strategy-courses" element={<StrategyCoursesPage />} />
          <Route path="/strategy-courses/:slug" element={<StrategyCourseDetailPage />} />
          <Route path="/:lang/strategy-courses/:slug" element={<StrategyCourseDetailPage />} />
          
          <Route path="/tools" element={<ToolsPage />} />
          <Route path="/:lang/tools" element={<ToolsPage />} />
          <Route path="/tools/:slug" element={<ToolDetailPage />} />
          <Route path="/:lang/tools/:slug" element={<ToolDetailPage />} />
          
          <Route path="/about" element={<AboutPage />} />
          <Route path="/:lang/about" element={<AboutPage />} />
          
          <Route path="/login" element={<LoginPage />} />
          <Route path="/:lang/login" element={<LoginPage />} />
          
          <Route path="/newsletter" element={<NewsletterPage />} />
          <Route path="/:lang/newsletter" element={<NewsletterPage />} />
          
          <Route path="/privacy" element={<PrivacyPage />} />
          <Route path="/:lang/privacy" element={<PrivacyPage />} />
          
          <Route path="*" element={<NotFoundPage />} />
        </Routes>
        </Suspense>
      </main>
      <Footer />
    </div>
  );
}

export default App;

