import styles from './Spinner.module.css';

const Spinner = ({ size = 'md' }) => {
    return (
        <div className={`${styles.spinner} ${styles[size]}`}>
            <svg viewBox="0 0 50 50">
                <circle
                    className={styles.circle}
                    cx="25"
                    cy="25"
                    r="20"
                    fill="none"
                    strokeWidth="4"
                />
            </svg>
        </div>
    );
};

export default Spinner;