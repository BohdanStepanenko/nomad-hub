import styles from './AuthLayout.module.css';

const AuthLayout = ({ children, title }) => {
    return (
        <div className={styles.container}>
            <div className={styles.glassEffect}>
                <div>
                    <h1 className={styles.title}>{title}</h1>
                    {children}
                </div>
            </div>
        </div>
    );
};

export default AuthLayout;