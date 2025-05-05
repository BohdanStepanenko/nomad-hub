import styles from './Select.module.css';

const Select = ({ label, options, ...props }) => {
    return (
        <div className={styles.selectGroup}>
            {label && <label className={styles.label}>{label}</label>}
            <select className={styles.select} {...props}>
                {options.map((option) => (
                    <option key={option.value} value={option.value}>
                        {option.label}
                    </option>
                ))}
            </select>
        </div>
    );
};

export default Select;
