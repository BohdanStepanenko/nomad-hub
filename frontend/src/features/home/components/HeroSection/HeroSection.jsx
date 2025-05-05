import { Link } from 'react-router-dom';
import { FiBarChart, FiBookOpen, FiTarget } from 'react-icons/fi';
import styles from './HeroSection.module.css';

const HeroSection = () => {
    return (
        <section className={styles.hero}>
            <div className={styles.content}>
                <h1 className={styles.title}>
                    AI-Powered Career Navigator
                </h1>
                <p className={styles.subtitle}>
                    Assess your skills, analyze real-time job market demands, and generate
                    personalized learning roadmaps powered by artificial intelligence
                </p>

                <div className={styles.ctaContainer}>
                    <Link to="/skill-assessment" className={styles.primaryCta}>
                        Start Free Assessment
                    </Link>
                    <Link to="/demo" className={styles.secondaryCta}>
                        Watch Demo
                    </Link>
                </div>

                <div className={styles.stats}>
                    <div className={styles.statItem}>
                        <FiBarChart className={styles.statIcon} />
                        <span>500K+ Skills Analyzed</span>
                    </div>
                    <div className={styles.statItem}>
                        <FiBookOpen className={styles.statIcon} />
                        <span>10K+ Learning Paths</span>
                    </div>
                    <div className={styles.statItem}>
                        <FiTarget className={styles.statIcon} />
                        <span>92% Career Success Rate</span>
                    </div>
                </div>
            </div>
        </section>
    );
};

export default HeroSection;