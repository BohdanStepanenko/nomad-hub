import HeroSection from '../components/HeroSection/HeroSection';
import FeaturesGrid from '../components/FeaturesGrid/FeaturesGrid';
import PricingSection from '../components/PricingSection/PricingSection';
import styles from './HomePage.module.css';

const HomePage = () => {
    return (
        <div className={styles.container}>
            <HeroSection />
            <FeaturesGrid />
            <PricingSection />
        </div>
    );
};

export default HomePage;