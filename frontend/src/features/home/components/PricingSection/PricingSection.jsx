import styles from './PricingSection.module.css';

const PricingSection = () => {
    return (
        <section className={styles.pricing}>
            <h2 className={styles.title}>Empower Your Career Growth</h2>
            <div className={styles.cards}>
                <div className={styles.card}>
                    <h3>Starter</h3>
                    <div className={styles.price}>$0<span>/month</span></div>
                    <ul className={styles.features}>
                        <li>Basic Skill Assessment</li>
                        <li>General Career Trends</li>
                        <li>3 Learning Roadmaps</li>
                        <li>Email Support</li>
                        <li>Community Access</li>
                    </ul>
                    <button className={styles.button}>Start Learning</button>
                </div>

                <div className={`${styles.card} ${styles.recommended}`}>
                    <div className={styles.badge}>Career Accelerator</div>
                    <h3>Professional</h3>
                    <div className={styles.price}>$29<span>/month</span></div>
                    <ul className={styles.features}>
                        <li>Advanced AI Analysis</li>
                        <li>Personalized Roadmaps</li>
                        <li>Job Market Predictions</li>
                        <li>Priority Support</li>
                        <li>Expert Mentorship</li>
                    </ul>
                    <button className={styles.button}>Boost Career</button>
                </div>

                <div className={styles.card}>
                    <h3>Enterprise</h3>
                    <div className={styles.price}>Custom</div>
                    <ul className={styles.features}>
                        <li>Team Skill Analytics</li>
                        <li>Custom AI Models</li>
                        <li>Dedicated Success Manager</li>
                        <li>HR Integration</li>
                        <li>24/7 Premium Support</li>
                    </ul>
                    <button className={styles.button}>Contact Team</button>
                </div>
            </div>
        </section>
    );
};

export default PricingSection;