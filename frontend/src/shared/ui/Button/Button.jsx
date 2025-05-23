import styles from './Button.module.css';

const Button = ({ children, className = '', ...props }) => {
    return (
        <button
            className={`${styles.button} ${className}`}
            {...props}
        >
            {children}
        </button>
    );
};

export default Button;