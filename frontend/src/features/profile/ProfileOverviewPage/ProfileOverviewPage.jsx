import React, { useContext } from 'react';
import { useNavigate } from 'react-router-dom';
import { UserContext } from '../../../context/UserContext';
import Button from '../../../shared/ui/Button/Button';
import styles from './ProfileOverviewPage.module.css';

const ProfileOverviewPage = () => {
    const { user, selfAssessments } = useContext(UserContext); // Предполагается, что selfAssessments хранятся в контексте
    const navigate = useNavigate();
console.log(selfAssessments);
    return (
        <div className={styles.container}>
            <header className={styles.header}>
                <h1 className={styles.pageTitle}>My Profile</h1>
                <Button onClick={() => navigate('/profile/edit')} className={styles.editButton}>
                    Edit Profile
                </Button>
            </header>

            <section className={styles.profileCard}>
                <div className={styles.avatarSection}>
                    <img
                        src={user?.avatarUrl || '/default-avatar.png'}
                        alt="Avatar"
                        className={styles.avatar}
                    />
                </div>
                <div className={styles.userInfo}>
                    <h2 className={styles.userName}>{user?.name || 'Your Name'}</h2>
                    <p className={styles.userEmail}>{user?.email}</p>
                </div>
            </section>

            <section className={styles.statsSection}>
                <div className={styles.stat}>
                    <span className={styles.statLabel}>Points</span>
                    <span className={styles.statValue}>{user?.points || 0}</span>
                </div>
                {/* Добавьте другие статистические блоки по необходимости */}
            </section>

            <section className={styles.assessmentSection}>
                <h3 className={styles.sectionTitle}>Self Assessments</h3>
                {selfAssessments && selfAssessments.length > 0 ? (
                    <div className={styles.assessmentList}>
                        {selfAssessments.map((item, index) => (
                            <div key={index} className={styles.assessmentItem}>
                                <span>{item.skillName}</span>
                                <span>{item.levelName}</span>
                                <span>{'★'.repeat(item.selfRating)}</span>
                            </div>
                        ))}
                    </div>
                ) : (
                    <p>No self assessments added yet.</p>
                )}
            </section>
        </div>
    );
};

export default ProfileOverviewPage;
