import { FiBookOpen, FiBarChart, FiTarget, FiUsers } from 'react-icons/fi';
import styles from './FeaturesGrid.module.css';

const FeaturesGrid = () => {
    return (
        <section className={styles.features}>
            <h2 className={styles.title}>Your Career Growth Engine</h2>
            <div className={styles.grid}>
                <div className={styles.card}>
                    <FiBookOpen className={styles.icon} />
                    <h3>AI Skill Assessment</h3>
                    <ul className={styles.list}>
                        <li>Comprehensive skill evaluation</li>
                        <li>Real-time competency analysis</li>
                        <li>Personalized gap detection</li>
                    </ul>
                </div>

                <div className={styles.card}>
                    <FiBarChart className={styles.icon} />
                    <h3>Market Intelligence</h3>
                    <ul className={styles.list}>
                        <li>Real-time job trend analysis</li>
                        <li>Salary benchmarking</li>
                        <li>Industry demand forecasting</li>
                    </ul>
                </div>

                <div className={styles.card}>
                    <FiTarget className={styles.icon} />
                    <h3>Smart Roadmapping</h3>
                    <ul className={styles.list}>
                        <li>Personalized learning paths</li>
                        <li>Adaptive milestone tracking</li>
                        <li>Career progression simulation</li>
                    </ul>
                </div>

                <div className={styles.card}>
                    <FiUsers className={styles.icon} />
                    <h3>Mentor Network</h3>
                    <ul className={styles.list}>
                        <li>AI-matched industry experts</li>
                        <li>1:1 career coaching sessions</li>
                        <li>Peer learning communities</li>
                    </ul>
                </div>
            </div>
        </section>
    );
};

export default FeaturesGrid;