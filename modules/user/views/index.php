<?php
$current_page = 'profile';
$title = 'Hồ Sơ';
?>

<style>
/* Luxury Minimalist Profile Styles */
.profile-luxury-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 2rem 1rem;
}

.profile-luxury-sidebar {
    background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
    border-radius: 24px;
    padding: 3rem 2rem;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    position: sticky;
    top: 2rem;
    height: fit-content;
    max-height: calc(100vh - 4rem);
    overflow-y: auto;
    overflow-x: hidden;
    position: relative;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    border: 2px solid transparent;
}

/* Animated Border Frame cho Sidebar */
.profile-luxury-sidebar::before {
    content: '';
    position: absolute;
    inset: -2px;
    border-radius: 24px;
    padding: 2px;
    background: linear-gradient(45deg, #d4af37, #f4e4bc, #d4af37, #ff6b6b, #d4af37);
    background-size: 300% 300%;
    -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
    -webkit-mask-composite: xor;
    mask-composite: exclude;
    animation: borderRotate 3s ease infinite;
    opacity: 0;
    transition: opacity 0.4s ease;
}

.profile-luxury-sidebar:hover::before {
    opacity: 1;
}

.profile-luxury-sidebar:hover {
    transform: translateY(-5px);
    box-shadow: 0 25px 70px rgba(212, 175, 55, 0.2), 0 0 40px rgba(212, 175, 55, 0.1);
}

@keyframes borderRotate {
    0%, 100% {
        background-position: 0% 50%;
    }
    50% {
        background-position: 100% 50%;
    }
}

/* Glowing Effect */
.profile-luxury-sidebar::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(212, 175, 55, 0.3) 0%, transparent 70%);
    transform: translate(-50%, -50%);
    transition: width 0.6s ease, height 0.6s ease;
    pointer-events: none;
    z-index: -1;
}

.profile-luxury-sidebar:hover::after {
    width: 200%;
    height: 200%;
}

.profile-luxury-sidebar::-webkit-scrollbar {
    width: 6px;
}

.profile-luxury-sidebar::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.05);
    border-radius: 3px;
}

.profile-luxury-sidebar::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.2);
    border-radius: 3px;
}

.profile-luxury-sidebar::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.3);
}

.profile-avatar-wrapper {
    position: relative;
    width: 160px;
    height: 160px;
    margin: 0 auto 2rem;
    cursor: pointer;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    z-index: 1;
}

/* Animated Frame cho Avatar */
.profile-avatar-wrapper::before {
    content: '';
    position: absolute;
    inset: -8px;
    border-radius: 50%;
    background: linear-gradient(45deg, #d4af37, #f4e4bc, #ff6b6b, #4ecdc4, #d4af37);
    background-size: 400% 400%;
    animation: avatarBorderSpin 4s linear infinite;
    opacity: 0;
    transition: opacity 0.4s ease;
    z-index: -1;
}

.profile-avatar-wrapper::after {
    content: '';
    position: absolute;
    inset: -4px;
    border-radius: 50%;
    background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
    z-index: -1;
}

@keyframes avatarBorderSpin {
    0% {
        background-position: 0% 50%;
        transform: rotate(0deg);
    }
    50% {
        background-position: 100% 50%;
    }
    100% {
        background-position: 0% 50%;
        transform: rotate(360deg);
    }
}

.profile-avatar-wrapper:hover {
    transform: scale(1.1) rotate(5deg);
}

.profile-avatar-wrapper:hover::before {
    opacity: 1;
}

.profile-avatar-wrapper:hover .avatar-overlay {
    opacity: 1;
}

/* Floating Animation */
@keyframes float {
    0%, 100% {
        transform: translateY(0px);
    }
    50% {
        transform: translateY(-10px);
    }
}

.profile-avatar-wrapper {
    animation: float 3s ease-in-out infinite;
}

.profile-avatar-container {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    overflow: hidden;
    border: 4px solid rgba(255, 255, 255, 0.1);
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);
    position: relative;
    transition: all 0.4s ease;
    z-index: 2;
}

.profile-avatar-container:hover {
    border-color: #d4af37;
    box-shadow: 0 0 30px rgba(212, 175, 55, 0.6), 0 10px 40px rgba(0, 0, 0, 0.5);
}

.profile-avatar-container img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.avatar-placeholder-luxury {
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, #2d2d2d 0%, #1a1a1a 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: rgba(255, 255, 255, 0.3);
    font-size: 4rem;
}

.avatar-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.6);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
    z-index: 2;
}

.avatar-overlay i {
    color: white;
    font-size: 2rem;
}

#avatar-input {
    display: none;
}

.btn-change-avatar {
    width: 100%;
    background: transparent;
    color: #d4af37;
    border: 2px solid #d4af37;
    padding: 0.75rem 1rem;
    border-radius: 12px;
    font-weight: 600;
    font-size: 0.9rem;
    cursor: pointer;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    margin-bottom: 1.5rem;
    letter-spacing: 0.3px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    position: relative;
    overflow: hidden;
}

/* Animated Background Effect */
.btn-change-avatar::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    border-radius: 50%;
    background: #d4af37;
    transform: translate(-50%, -50%);
    transition: width 0.6s ease, height 0.6s ease;
    z-index: 0;
}

.btn-change-avatar:hover::before {
    width: 300px;
    height: 300px;
}

.btn-change-avatar span,
.btn-change-avatar i {
    position: relative;
    z-index: 1;
    transition: all 0.3s ease;
}

.btn-change-avatar:hover {
    color: #1a1a1a;
    transform: translateY(-3px) scale(1.02);
    box-shadow: 0 8px 25px rgba(212, 175, 55, 0.4), 0 0 20px rgba(212, 175, 55, 0.2);
    border-color: #f4e4bc;
}

.btn-change-avatar:hover i {
    transform: rotate(15deg) scale(1.2);
}

.profile-name-luxury {
    text-align: center;
    margin-bottom: 1.5rem;
    position: relative;
    padding: 1rem;
    border-radius: 16px;
    transition: all 0.4s ease;
}

/* Glow Effect cho Name Section */
.profile-name-luxury::before {
    content: '';
    position: absolute;
    inset: 0;
    border-radius: 16px;
    background: radial-gradient(circle, rgba(212, 175, 55, 0.1) 0%, transparent 70%);
    opacity: 0;
    transition: opacity 0.4s ease;
    pointer-events: none;
}

.profile-luxury-sidebar:hover .profile-name-luxury::before {
    opacity: 1;
}

.profile-name-luxury h2 {
    font-size: 1.75rem;
    font-weight: 600;
    color: #ffffff;
    margin-bottom: 0.5rem;
    letter-spacing: -0.5px;
    position: relative;
    transition: all 0.4s ease;
    background: linear-gradient(135deg, #ffffff 0%, #d4af37 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.profile-luxury-sidebar:hover .profile-name-luxury h2 {
    transform: scale(1.05);
    text-shadow: 0 0 20px rgba(212, 175, 55, 0.5);
}

.profile-role-luxury {
    color: rgba(255, 255, 255, 0.6);
    font-size: 0.9rem;
    font-weight: 400;
    letter-spacing: 0.5px;
    text-transform: uppercase;
}

.profile-badge-luxury {
    display: inline-block;
    background: linear-gradient(135deg, #d4af37 0%, #f4e4bc 100%);
    color: #1a1a1a;
    padding: 0.4rem 1rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    margin-top: 0.5rem;
    letter-spacing: 0.5px;
    position: relative;
    overflow: hidden;
    transition: all 0.4s ease;
    box-shadow: 0 4px 15px rgba(212, 175, 55, 0.3);
}

/* Animated Shine Effect */
.profile-badge-luxury::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    transform: rotate(45deg);
    transition: all 0.6s ease;
    opacity: 0;
}

.profile-badge-luxury:hover::before {
    animation: badgeShine 1.5s ease;
}

@keyframes badgeShine {
    0% {
        transform: translateX(-100%) translateY(-100%) rotate(45deg);
        opacity: 0;
    }
    50% {
        opacity: 1;
    }
    100% {
        transform: translateX(100%) translateY(100%) rotate(45deg);
        opacity: 0;
    }
}

.profile-badge-luxury:hover {
    transform: scale(1.1) rotate(2deg);
    box-shadow: 0 6px 25px rgba(212, 175, 55, 0.5), 0 0 30px rgba(212, 175, 55, 0.3);
    background: linear-gradient(135deg, #f4e4bc 0%, #d4af37 100%);
}

.btn-upgrade-luxury {
    width: 100%;
    background: linear-gradient(135deg, #d4af37 0%, #f4e4bc 100%);
    color: #1a1a1a;
    border: none;
    padding: 1rem;
    border-radius: 12px;
    font-weight: 600;
    font-size: 0.95rem;
    cursor: pointer;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    margin-bottom: 2rem;
    box-shadow: 0 4px 15px rgba(212, 175, 55, 0.3);
    letter-spacing: 0.3px;
    position: relative;
    overflow: hidden;
}

/* Animated Gradient */
.btn-upgrade-luxury::before {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, #f4e4bc 0%, #d4af37 50%, #f4e4bc 100%);
    background-size: 200% 200%;
    opacity: 0;
    transition: opacity 0.4s ease;
}

.btn-upgrade-luxury:hover::before {
    opacity: 1;
    animation: upgradeGradient 2s ease infinite;
}

@keyframes upgradeGradient {
    0%, 100% {
        background-position: 0% 50%;
    }
    50% {
        background-position: 100% 50%;
    }
}

.btn-upgrade-luxury span,
.btn-upgrade-luxury i {
    position: relative;
    z-index: 1;
}

.btn-upgrade-luxury:hover {
    transform: translateY(-4px) scale(1.03);
    box-shadow: 0 10px 30px rgba(212, 175, 55, 0.5), 0 0 40px rgba(212, 175, 55, 0.3);
}

.btn-upgrade-luxury:hover i {
    transform: rotate(360deg) scale(1.2);
    transition: transform 0.6s ease;
}

/* Sparkle Effect */
.btn-upgrade-luxury::after {
    content: '✨';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    font-size: 1.5rem;
    opacity: 0;
    transition: all 0.4s ease;
    z-index: 2;
    pointer-events: none;
}

.btn-upgrade-luxury:hover::after {
    animation: sparkle 1s ease;
}

@keyframes sparkle {
    0% {
        opacity: 0;
        transform: translate(-50%, -50%) scale(0) rotate(0deg);
    }
    50% {
        opacity: 1;
        transform: translate(-50%, -50%) scale(1.5) rotate(180deg);
    }
    100% {
        opacity: 0;
        transform: translate(-50%, -50%) scale(0) rotate(360deg);
    }
}

.balance-card-luxury {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 16px;
    padding: 1.5rem;
    margin-bottom: 2rem;
    backdrop-filter: blur(10px);
    position: relative;
    overflow: hidden;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Animated Frame cho Balance Card */
.balance-card-luxury::before {
    content: '';
    position: absolute;
    inset: -2px;
    border-radius: 16px;
    padding: 2px;
    background: linear-gradient(135deg, #d4af37, transparent, #d4af37, transparent);
    background-size: 200% 200%;
    -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
    -webkit-mask-composite: xor;
    mask-composite: exclude;
    animation: balanceBorderFlow 3s linear infinite;
    opacity: 0;
    transition: opacity 0.4s ease;
}

.balance-card-luxury:hover::before {
    opacity: 1;
}

.balance-card-luxury:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 40px rgba(212, 175, 55, 0.2);
    border-color: rgba(212, 175, 55, 0.3);
}

@keyframes balanceBorderFlow {
    0% {
        background-position: 0% 0%;
    }
    100% {
        background-position: 200% 200%;
    }
}

/* Shimmer Effect */
.balance-card-luxury::after {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: linear-gradient(45deg, transparent, rgba(212, 175, 55, 0.1), transparent);
    transform: rotate(45deg);
    transition: all 0.6s ease;
    opacity: 0;
}

.balance-card-luxury:hover::after {
    animation: shimmer 1.5s ease;
}

@keyframes shimmer {
    0% {
        transform: translateX(-100%) translateY(-100%) rotate(45deg);
        opacity: 0;
    }
    50% {
        opacity: 1;
    }
    100% {
        transform: translateX(100%) translateY(100%) rotate(45deg);
        opacity: 0;
    }
}

.balance-header-luxury {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 1rem;
    color: rgba(255, 255, 255, 0.7);
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 1px;
    font-weight: 500;
}

.balance-header-luxury i {
    color: #d4af37;
    font-size: 1.1rem;
}

.balance-amount-luxury {
    font-size: 1.75rem;
    font-weight: 700;
    color: #ffffff;
    margin-bottom: 1rem;
    letter-spacing: -0.5px;
}

.btn-deposit-luxury {
    width: 100%;
    background: transparent;
    color: #d4af37;
    border: 2px solid #d4af37;
    padding: 0.75rem;
    border-radius: 10px;
    font-weight: 600;
    font-size: 0.9rem;
    cursor: pointer;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    letter-spacing: 0.3px;
    position: relative;
    overflow: hidden;
}

/* Animated Border */
.btn-deposit-luxury::before {
    content: '';
    position: absolute;
    inset: -2px;
    border-radius: 10px;
    padding: 2px;
    background: linear-gradient(45deg, #d4af37, #f4e4bc, #4ecdc4, #d4af37);
    background-size: 300% 300%;
    -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
    -webkit-mask-composite: xor;
    mask-composite: exclude;
    opacity: 0;
    transition: opacity 0.4s ease;
    animation: depositBorder 3s linear infinite;
}

.btn-deposit-luxury:hover::before {
    opacity: 1;
}

@keyframes depositBorder {
    0% {
        background-position: 0% 50%;
    }
    100% {
        background-position: 300% 50%;
    }
}

/* Fill Effect */
.btn-deposit-luxury::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    border-radius: 50%;
    background: #d4af37;
    transform: translate(-50%, -50%);
    transition: width 0.6s ease, height 0.6s ease;
    z-index: 0;
}

.btn-deposit-luxury:hover::after {
    width: 300px;
    height: 300px;
}

.btn-deposit-luxury span,
.btn-deposit-luxury i {
    position: relative;
    z-index: 1;
    transition: all 0.3s ease;
}

.btn-deposit-luxury:hover {
    color: #1a1a1a;
    transform: translateY(-3px) scale(1.02);
    box-shadow: 0 8px 25px rgba(212, 175, 55, 0.4);
}

.btn-deposit-luxury:hover i {
    transform: rotate(180deg) scale(1.2);
}

.profile-menu-luxury {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.menu-item-luxury {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem 1.25rem;
    color: rgba(255, 255, 255, 0.7);
    text-decoration: none;
    border-radius: 12px;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    font-weight: 500;
    font-size: 0.95rem;
    position: relative;
    overflow: hidden;
}

/* Animated Background cho Menu Items */
.menu-item-luxury::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    height: 100%;
    width: 4px;
    background: linear-gradient(180deg, #d4af37, #f4e4bc);
    transform: scaleY(0);
    transform-origin: bottom;
    transition: transform 0.4s ease;
}

.menu-item-luxury:hover::before {
    transform: scaleY(1);
    transform-origin: top;
}

.menu-item-luxury::after {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(90deg, rgba(212, 175, 55, 0.1), transparent);
    opacity: 0;
    transition: opacity 0.4s ease;
}

.menu-item-luxury:hover::after {
    opacity: 1;
}

.menu-item-luxury:hover {
    background: rgba(255, 255, 255, 0.08);
    color: #ffffff;
    transform: translateX(8px) scale(1.02);
    box-shadow: -4px 0 15px rgba(212, 175, 55, 0.2);
    padding-left: 1.5rem;
}

.menu-item-luxury i {
    width: 24px;
    text-align: center;
    font-size: 1.1rem;
    transition: all 0.4s ease;
    position: relative;
    z-index: 1;
}

.menu-item-luxury:hover i {
    transform: scale(1.2) rotate(5deg);
    color: #d4af37;
}

.menu-item-luxury span {
    position: relative;
    z-index: 1;
}

.menu-item-luxury.logout {
    color: rgba(255, 107, 107, 0.8);
    margin-top: 1rem;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    padding-top: 1.5rem;
}

.menu-item-luxury.logout:hover {
    background: rgba(255, 107, 107, 0.1);
    color: #ff6b6b;
}

.profile-content-luxury {
    padding-left: 2rem;
}

/* Đảm bảo phần content bên phải scroll tự do */
.col-lg-8 {
    position: relative;
}

.card-luxury {
    background: #1f1f1f;
    border-radius: 24px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);
    margin-bottom: 2rem;
    overflow: hidden;
    border: 1px solid rgba(255, 255, 255, 0.1);
    position: relative;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Unique Frame Design cho Cards */
.card-luxury::before {
    content: '';
    position: absolute;
    inset: -3px;
    border-radius: 24px;
    padding: 3px;
    background: linear-gradient(135deg, 
        #d4af37 0%, 
        transparent 25%, 
        transparent 75%, 
        #d4af37 100%,
        transparent 125%,
        #4ecdc4 150%,
        transparent 175%,
        #ff6b6b 200%);
    background-size: 300% 300%;
    -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
    -webkit-mask-composite: xor;
    mask-composite: exclude;
    animation: cardBorderAnimation 4s ease infinite;
    opacity: 0;
    transition: opacity 0.4s ease;
    z-index: 0;
}

.card-luxury:hover::before {
    opacity: 1;
}

.card-luxury:hover {
    transform: translateY(-8px) scale(1.01);
    box-shadow: 0 20px 60px rgba(212, 175, 55, 0.15), 0 0 50px rgba(212, 175, 55, 0.1);
    border-color: rgba(212, 175, 55, 0.3);
}

@keyframes cardBorderAnimation {
    0%, 100% {
        background-position: 0% 0%;
    }
    25% {
        background-position: 100% 0%;
    }
    50% {
        background-position: 100% 100%;
    }
    75% {
        background-position: 0% 100%;
    }
}

/* Corner Accents */
.card-luxury::after {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, rgba(212, 175, 55, 0.2), transparent);
    border-radius: 0 24px 0 100%;
    opacity: 0;
    transition: opacity 0.4s ease;
    pointer-events: none;
}

.card-luxury:hover::after {
    opacity: 1;
}

.card-header-luxury {
    padding: 2rem 2.5rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    background: linear-gradient(135deg, #2a2a2a 0%, #1f1f1f 100%);
    position: relative;
    overflow: hidden;
    transition: all 0.4s ease;
}

/* Animated Background Pattern */
.card-header-luxury::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(212, 175, 55, 0.1) 0%, transparent 70%);
    animation: headerPulse 3s ease-in-out infinite;
    opacity: 0;
    transition: opacity 0.4s ease;
}

.card-luxury:hover .card-header-luxury::before {
    opacity: 1;
}

@keyframes headerPulse {
    0%, 100% {
        transform: scale(0.8);
        opacity: 0;
    }
    50% {
        transform: scale(1.2);
        opacity: 0.3;
    }
}

.card-header-luxury h3 {
    position: relative;
    z-index: 1;
}

.card-luxury:hover .card-header-luxury {
    background: linear-gradient(135deg, #2f2f2f 0%, #242424 100%);
    border-bottom-color: rgba(212, 175, 55, 0.2);
}

.card-header-luxury h3 {
    font-size: 1.5rem;
    font-weight: 600;
    color: #ffffff;
    margin: 0;
    letter-spacing: -0.5px;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.card-header-luxury h3 i {
    color: #d4af37;
    font-size: 1.3rem;
}

.card-body-luxury {
    padding: 2.5rem;
}

.form-group-luxury {
    margin-bottom: 2rem;
}

.form-label-luxury {
    display: block;
    font-size: 0.9rem;
    font-weight: 600;
    color: rgba(255, 255, 255, 0.8);
    margin-bottom: 0.75rem;
    letter-spacing: 0.3px;
    text-transform: uppercase;
}

.form-control-luxury {
    width: 100%;
    padding: 1rem 1.25rem;
    border: 2px solid rgba(255, 255, 255, 0.1);
    border-radius: 12px;
    font-size: 1rem;
    color: #ffffff;
    background: rgba(255, 255, 255, 0.05);
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    font-family: inherit;
    position: relative;
}

.form-control-luxury::before {
    content: '';
    position: absolute;
    inset: -2px;
    border-radius: 12px;
    padding: 2px;
    background: linear-gradient(45deg, #d4af37, #f4e4bc, #d4af37);
    background-size: 200% 200%;
    -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
    -webkit-mask-composite: xor;
    mask-composite: exclude;
    opacity: 0;
    transition: opacity 0.4s ease;
    z-index: -1;
}

.form-control-luxury:focus {
    outline: none;
    border-color: #d4af37;
    box-shadow: 0 0 0 4px rgba(212, 175, 55, 0.2), 0 0 20px rgba(212, 175, 55, 0.1);
    background: rgba(255, 255, 255, 0.08);
    transform: translateY(-2px);
}

.form-control-luxury:focus::before {
    opacity: 1;
    animation: inputBorderFlow 2s linear infinite;
}

@keyframes inputBorderFlow {
    0% {
        background-position: 0% 50%;
    }
    100% {
        background-position: 200% 50%;
    }
}

.form-control-luxury::placeholder {
    color: rgba(255, 255, 255, 0.4);
}

.btn-update-luxury {
    background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
    color: #ffffff;
    border: none;
    padding: 1rem 2.5rem;
    border-radius: 12px;
    font-weight: 600;
    font-size: 1rem;
    cursor: pointer;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    letter-spacing: 0.3px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    position: relative;
    overflow: hidden;
}

/* Animated Gradient Background */
.btn-update-luxury::before {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, #d4af37 0%, #f4e4bc 50%, #d4af37 100%);
    background-size: 200% 200%;
    opacity: 0;
    transition: opacity 0.4s ease;
    z-index: 0;
}

.btn-update-luxury:hover::before {
    opacity: 1;
    animation: buttonGradient 2s ease infinite;
}

.btn-update-luxury span,
.btn-update-luxury i {
    position: relative;
    z-index: 1;
}

.btn-update-luxury:hover {
    transform: translateY(-3px) scale(1.05);
    box-shadow: 0 8px 25px rgba(212, 175, 55, 0.4), 0 0 30px rgba(212, 175, 55, 0.2);
    color: #1a1a1a;
}

@keyframes buttonGradient {
    0%, 100% {
        background-position: 0% 50%;
    }
    50% {
        background-position: 100% 50%;
    }
}

/* Ripple Effect */
.btn-update-luxury::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.3);
    transform: translate(-50%, -50%);
    transition: width 0.6s ease, height 0.6s ease;
    z-index: 1;
}

.btn-update-luxury:active::after {
    width: 300px;
    height: 300px;
}

.history-item-luxury {
    display: flex;
    gap: 1.5rem;
    padding: 1.5rem;
    border-radius: 16px;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    text-decoration: none;
    color: inherit;
    border: 1px solid rgba(255, 255, 255, 0.1);
    margin-bottom: 1rem;
    background: rgba(255, 255, 255, 0.03);
    position: relative;
    overflow: hidden;
}

/* Animated Border cho History Items */
.history-item-luxury::before {
    content: '';
    position: absolute;
    inset: -2px;
    border-radius: 16px;
    padding: 2px;
    background: linear-gradient(45deg, #d4af37, transparent, #4ecdc4, transparent, #d4af37);
    background-size: 300% 300%;
    -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
    -webkit-mask-composite: xor;
    mask-composite: exclude;
    opacity: 0;
    transition: opacity 0.4s ease;
    animation: historyBorder 3s linear infinite;
}

.history-item-luxury:hover::before {
    opacity: 1;
}

@keyframes historyBorder {
    0% {
        background-position: 0% 0%;
    }
    100% {
        background-position: 300% 300%;
    }
}

.history-item-luxury:hover {
    background: rgba(255, 255, 255, 0.08);
    transform: translateX(10px) scale(1.02);
    box-shadow: 0 8px 25px rgba(212, 175, 55, 0.2), -4px 0 15px rgba(212, 175, 55, 0.1);
    text-decoration: none;
    color: inherit;
    border-color: rgba(212, 175, 55, 0.3);
}

/* Glow Effect */
.history-item-luxury::after {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(212, 175, 55, 0.1), transparent);
    transition: left 0.6s ease;
}

.history-item-luxury:hover::after {
    left: 100%;
}

.history-thumbnail-luxury {
    width: 80px;
    height: 120px;
    border-radius: 12px;
    object-fit: cover;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    flex-shrink: 0;
}

.history-content-luxury h6 {
    font-size: 1.1rem;
    font-weight: 600;
    color: #ffffff;
    margin-bottom: 0.5rem;
    letter-spacing: -0.3px;
}

.history-time-luxury {
    color: rgba(255, 255, 255, 0.6);
    font-size: 0.9rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.empty-state-luxury {
    text-align: center;
    padding: 4rem 2rem;
    color: rgba(255, 255, 255, 0.5);
    position: relative;
}

/* Animated Background Pattern */
.empty-state-luxury::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 200px;
    height: 200px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(212, 175, 55, 0.1) 0%, transparent 70%);
    transform: translate(-50%, -50%);
    animation: emptyStatePulse 3s ease-in-out infinite;
    pointer-events: none;
}

@keyframes emptyStatePulse {
    0%, 100% {
        transform: translate(-50%, -50%) scale(0.8);
        opacity: 0.3;
    }
    50% {
        transform: translate(-50%, -50%) scale(1.2);
        opacity: 0.6;
    }
}

.empty-state-luxury i {
    font-size: 4rem;
    margin-bottom: 1rem;
    opacity: 0.4;
    transition: all 0.4s ease;
    display: inline-block;
    animation: emptyStateFloat 3s ease-in-out infinite;
}

@keyframes emptyStateFloat {
    0%, 100% {
        transform: translateY(0px) rotate(0deg);
    }
    50% {
        transform: translateY(-15px) rotate(5deg);
    }
}

.empty-state-luxury:hover i {
    opacity: 0.7;
    transform: scale(1.1);
    color: #d4af37;
}

.ticket-item-luxury {
    padding: 1.5rem;
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 16px;
    margin-bottom: 1rem;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    background: rgba(255, 255, 255, 0.03);
    position: relative;
    overflow: hidden;
}

/* Unique Frame Design cho Tickets */
.ticket-item-luxury::before {
    content: '';
    position: absolute;
    inset: 0;
    border-radius: 16px;
    padding: 2px;
    background: linear-gradient(135deg, 
        #d4af37 0%, 
        transparent 20%,
        #4ecdc4 40%,
        transparent 60%,
        #ff6b6b 80%,
        transparent 100%);
    background-size: 200% 200%;
    -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
    -webkit-mask-composite: xor;
    mask-composite: exclude;
    opacity: 0;
    transition: opacity 0.4s ease;
    animation: ticketBorder 4s linear infinite;
}

.ticket-item-luxury:hover::before {
    opacity: 1;
}

@keyframes ticketBorder {
    0% {
        background-position: 0% 0%;
    }
    50% {
        background-position: 100% 100%;
    }
    100% {
        background-position: 0% 0%;
    }
}

/* 3D Lift Effect */
.ticket-item-luxury:hover {
    box-shadow: 0 10px 30px rgba(212, 175, 55, 0.2), 0 0 40px rgba(212, 175, 55, 0.1);
    transform: translateY(-5px) rotateX(2deg);
    background: rgba(255, 255, 255, 0.06);
    border-color: rgba(212, 175, 55, 0.4);
}

/* Corner Highlight */
.ticket-item-luxury::after {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 80px;
    height: 80px;
    background: radial-gradient(circle, rgba(212, 175, 55, 0.2), transparent 70%);
    border-radius: 0 16px 0 100%;
    opacity: 0;
    transition: opacity 0.4s ease;
    pointer-events: none;
}

.ticket-item-luxury:hover::after {
    opacity: 1;
}

.ticket-header-luxury {
    display: flex;
    justify-content: space-between;
    align-items: start;
    margin-bottom: 1rem;
}

.ticket-title-luxury {
    font-size: 1.1rem;
    font-weight: 600;
    color: #ffffff;
    margin: 0;
}

.badge-luxury {
    padding: 0.4rem 1rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    letter-spacing: 0.3px;
    position: relative;
    transition: all 0.4s ease;
    overflow: hidden;
}

/* Pulse Effect cho Badges */
.badge-luxury::before {
    content: '';
    position: absolute;
    inset: 0;
    border-radius: 20px;
    background: currentColor;
    opacity: 0.2;
    animation: badgePulse 2s ease-in-out infinite;
}

@keyframes badgePulse {
    0%, 100% {
        transform: scale(1);
        opacity: 0.2;
    }
    50% {
        transform: scale(1.1);
        opacity: 0.4;
    }
}

.badge-luxury:hover {
    transform: scale(1.1);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
}

.badge-success-luxury {
    background: rgba(46, 213, 115, 0.1);
    color: #2ed573;
}

.badge-danger-luxury {
    background: rgba(255, 107, 107, 0.1);
    color: #ff6b6b;
}

.badge-warning-luxury {
    background: rgba(255, 184, 0, 0.1);
    color: #ffb800;
}

.ticket-info-luxury {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 0.75rem;
    color: rgba(255, 255, 255, 0.7);
    font-size: 0.9rem;
}

.ticket-info-luxury i {
    color: #d4af37;
    width: 18px;
}

/* Additional Smooth Transitions */
* {
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
}

/* Smooth Scroll */
html {
    scroll-behavior: smooth;
}

/* Performance Optimization */
.profile-luxury-sidebar,
.card-luxury,
.history-item-luxury,
.ticket-item-luxury {
    will-change: transform;
    backface-visibility: hidden;
}

/* Custom Scrollbar Enhancement */
.profile-luxury-sidebar::-webkit-scrollbar-thumb {
    background: linear-gradient(180deg, #d4af37, #f4e4bc);
    border-radius: 3px;
}

.profile-luxury-sidebar::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(180deg, #f4e4bc, #d4af37);
}

/* Loading Animation for Images */
.profile-avatar-container img,
.history-thumbnail-luxury {
    transition: opacity 0.3s ease;
}

.profile-avatar-container img:hover,
.history-thumbnail-luxury:hover {
    opacity: 0.9;
    transform: scale(1.05);
}

/* Enhanced Focus States for Accessibility */
.form-control-luxury:focus,
.btn-change-avatar:focus,
.btn-update-luxury:focus,
.btn-upgrade-luxury:focus,
.btn-deposit-luxury:focus {
    outline: 2px solid #d4af37;
    outline-offset: 2px;
}

@media (max-width: 992px) {
    .profile-content-luxury {
        padding-left: 0;
        margin-top: 2rem;
    }
    
    .profile-luxury-sidebar {
        position: relative;
        top: 0;
    }
    
    /* Reduce animations on mobile for performance */
    .profile-avatar-wrapper {
        animation: none;
    }
    
    .profile-luxury-sidebar::before,
    .card-luxury::before,
    .history-item-luxury::before,
    .ticket-item-luxury::before {
        animation-duration: 6s;
    }
}
</style>

<section class="section py-4" style="background: rgb(60, 60, 60); min-height: 100vh;">
    <div class="profile-luxury-container">
        <div class="row g-4">
            <!-- Sidebar -->
            <div class="col-lg-4 col-md-12">
                <div class="profile-luxury-sidebar">
                    <!-- Profile Avatar -->
                    <div class="profile-avatar-wrapper" onclick="document.getElementById('avatar-input').click()">
                        <div class="profile-avatar-container">
                            <?php if ($user['avatar']): ?>
                                <img src="<?php echo htmlspecialchars($user['avatar']); ?>" alt="Avatar" id="avatar-preview">
                            <?php else: ?>
                                <div class="avatar-placeholder-luxury">
                                    <i class="fas fa-user"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="avatar-overlay">
                            <i class="fas fa-camera"></i>
                        </div>
                    </div>
                    <input type="file" id="avatar-input" name="avatar" accept="image/*" style="display: none;">
                    
                    <!-- Change Avatar Button -->
                    <button class="btn-change-avatar" onclick="document.getElementById('avatar-input').click()">
                        <i class="fas fa-camera"></i>
                        <span><?php echo $user['avatar'] ? 'Đổi ảnh đại diện' : 'Thêm ảnh đại diện'; ?></span>
                    </button>
                    
                    <!-- Profile Name -->
                    <div class="profile-name-luxury">
                        <h2><?php echo htmlspecialchars($user['name']); ?></h2>
                        <p class="profile-role-luxury"><?php echo htmlspecialchars($userRole ?? 'Thành viên'); ?></p>
                        <?php if (isset($subscription) && $subscription && in_array(strtolower($subscription['name']), ['gold', 'premium', 'pro vip'])): ?>
                            <span class="profile-badge-luxury">Pro Vip</span>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Upgrade Button -->
                    <button class="btn-upgrade-luxury" onclick="openUpgradeModal()">
                        <i class="fas fa-crown me-2"></i> Nâng cấp gói ngay
                    </button>
                    
                    <!-- Balance Section -->
                    <div class="balance-card-luxury">
                        <div class="balance-header-luxury">
                            <i class="fas fa-coins"></i>
                            <span>Số dư (điểm)</span>
                        </div>
                        <div class="balance-amount-luxury">
                            <?php echo number_format($balance ?? 0, 0, ',', '.'); ?> điểm
                        </div>
                        <button class="btn-deposit-luxury" onclick="alert('Tính năng nạp điểm sẽ được thêm sau!');">
                            <i class="fas fa-plus me-2"></i> Nạp điểm
                        </button>
                    </div>
                    
                    <!-- Menu -->
                    <div class="profile-menu-luxury">
                        <a href="#" class="menu-item-luxury" onclick="document.getElementById('avatar-input').click(); return false;">
                            <i class="fas fa-user-circle"></i>
                            <span>Đổi ảnh đại diện</span>
                        </a>
                        <?php 
                        // Debug: Hiển thị link nếu user có role moderator hoặc có theater_id
                        $showModeratorLink = false;
                        if (isset($isModerator) && $isModerator) {
                            $showModeratorLink = true;
                        } elseif (isset($user['theater_id']) && !empty($user['theater_id'])) {
                            // Nếu user có theater_id được gán, cũng hiển thị link
                            $showModeratorLink = true;
                        } elseif (isset($user['role']) && $user['role'] === 'moderator') {
                            $showModeratorLink = true;
                        } elseif (isset($user['roles']) && !empty($user['roles'])) {
                            foreach ($user['roles'] as $role) {
                                if (isset($role['name']) && ($role['name'] === 'Moderator' || $role['name'] === 'Theater Manager')) {
                                    $showModeratorLink = true;
                                    break;
                                }
                            }
                        }
                        ?>
                        <?php if ($showModeratorLink): ?>
                            <a href="?route=moderator/index" class="menu-item-luxury">
                                <i class="fas fa-building"></i>
                                <span>Quản lý rạp</span>
                            </a>
                        <?php endif; ?>
                        <a href="#" class="menu-item-luxury">
                            <i class="fas fa-list"></i>
                            <span>Danh sách</span>
                        </a>
                        <a href="#history" class="menu-item-luxury">
                            <i class="fas fa-history"></i>
                            <span>Lịch sử</span>
                        </a>
                        <a href="#" class="menu-item-luxury">
                            <i class="fas fa-heart"></i>
                            <span>Yêu thích</span>
                        </a>
                        <a href="?route=auth/logout" class="menu-item-luxury logout">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Đăng xuất</span>
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="col-lg-8 col-md-12">
                <!-- Personal Info -->
                <div class="card-luxury">
                    <div class="card-header-luxury">
                        <h3>
                            <i class="fas fa-user-edit"></i>
                            Thông tin cá nhân
                        </h3>
                    </div>
                    <div class="card-body-luxury">
                        <form method="POST" action="?route=profile/update" enctype="multipart/form-data">
                            <div class="form-group-luxury">
                                <label for="name" class="form-label-luxury">Họ và tên</label>
                                <input type="text" class="form-control-luxury" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                            </div>
                            <div class="form-group-luxury">
                                <label for="email" class="form-label-luxury">Email</label>
                                <input type="email" class="form-control-luxury" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                            </div>
                            <div class="form-group-luxury">
                                <label for="birthdate" class="form-label-luxury">Ngày sinh</label>
                                <input type="date" class="form-control-luxury" id="birthdate" name="birthdate" value="<?php echo $user['birthdate']; ?>">
                            </div>
                            <button type="submit" class="btn-update-luxury">
                                <i class="fas fa-save me-2"></i> Cập nhật
                            </button>
                        </form>
                    </div>
                </div>
                
                <!-- Watch History -->
                <div class="card-luxury" id="history">
                    <div class="card-header-luxury">
                        <h3>
                            <i class="fas fa-history"></i>
                            Lịch sử xem phim
                        </h3>
                    </div>
                    <div class="card-body-luxury">
                        <?php if (empty($history)): ?>
                            <div class="empty-state-luxury">
                                <i class="fas fa-history"></i>
                                <p>Chưa có lịch sử xem phim.</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($history as $item): ?>
                                <a href="?route=movie/watch&id=<?php echo $item['movie_id']; ?>" class="history-item-luxury">
                                    <?php if ($item['thumbnail']): ?>
                                        <img src="<?php echo htmlspecialchars($item['thumbnail']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>" class="history-thumbnail-luxury">
                                    <?php endif; ?>
                                    <div class="history-content-luxury">
                                        <h6><?php echo htmlspecialchars($item['title']); ?></h6>
                                        <div class="history-time-luxury">
                                            <i class="fas fa-clock"></i>
                                            <span><?php echo date('d/m/Y H:i', strtotime($item['created_at'])); ?></span>
                                        </div>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Tickets -->
                <div class="card-luxury">
                    <div class="card-header-luxury">
                        <h3>
                            <i class="fas fa-ticket-alt"></i>
                            Vé của tôi
                        </h3>
                    </div>
                    <div class="card-body-luxury">
                        <?php if (empty($tickets)): ?>
                            <div class="empty-state-luxury">
                                <i class="fas fa-ticket-alt"></i>
                                <p>Bạn chưa có vé nào.</p>
                                <a href="?route=booking/index" class="btn-update-luxury mt-3" style="display: inline-block;">
                                    <i class="fas fa-shopping-cart me-2"></i> Đặt vé ngay
                                </a>
                            </div>
                        <?php else: ?>
                            <?php 
                            $displayTickets = array_slice($tickets, 0, 5);
                            foreach ($displayTickets as $ticket): 
                            ?>
                                <div class="ticket-item-luxury">
                                    <div class="ticket-header-luxury">
                                        <h6 class="ticket-title-luxury"><?php echo htmlspecialchars($ticket['movie_title']); ?></h6>
                                        <span class="badge-luxury <?php 
                                            echo $ticket['status'] === 'Đã đặt' ? 'badge-success-luxury' : 
                                                ($ticket['status'] === 'Đã hủy' ? 'badge-danger-luxury' : 'badge-warning-luxury'); 
                                        ?>">
                                            <?php echo htmlspecialchars($ticket['status']); ?>
                                        </span>
                                    </div>
                                    <div class="ticket-info-luxury">
                                        <div>
                                            <i class="fas fa-building"></i>
                                            <?php echo htmlspecialchars($ticket['theater_name']); ?>
                                        </div>
                                        <div>
                                            <i class="fas fa-calendar"></i>
                                            <?php echo date('d/m/Y', strtotime($ticket['show_date'])); ?>
                                        </div>
                                        <div>
                                            <i class="fas fa-clock"></i>
                                            <?php echo date('H:i', strtotime($ticket['show_time'])); ?>
                                        </div>
                                        <div>
                                            <i class="fas fa-chair"></i>
                                            Ghế: <?php echo htmlspecialchars($ticket['seat']); ?>
                                        </div>
                                        <div>
                                            <i class="fas fa-money-bill"></i>
                                            <?php echo number_format($ticket['price']); ?> đ
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            <?php if (count($tickets) > 5): ?>
                                <div class="text-center mt-3">
                                    <a href="?route=booking/my-tickets" class="btn-update-luxury" style="display: inline-block;">
                                        <i class="fas fa-eye me-2"></i> Xem tất cả vé (<?php echo count($tickets); ?>)
                                    </a>
                                </div>
                            <?php else: ?>
                                <div class="text-center mt-3">
                                    <a href="?route=booking/my-tickets" class="btn-update-luxury" style="display: inline-block;">
                                        <i class="fas fa-eye me-2"></i> Xem tất cả vé
                                    </a>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Upgrade Subscription Modal -->
<div class="modal fade" id="upgradeModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nâng cấp gói</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Điểm hiện tại của bạn: <strong><?php echo number_format($balance ?? 0, 0, ',', '.'); ?> điểm</strong>
                </div>
                
                <div class="row g-3">
                    <?php foreach ($allSubscriptions as $sub): ?>
                        <?php 
                        $subPrice = intval($sub['price']);
                        $canAfford = ($balance ?? 0) >= $subPrice;
                        $isCurrent = isset($subscription) && $subscription && $subscription['id'] == $sub['id'];
                        $isHigher = isset($subscription) && $subscription && intval($subscription['price']) >= $subPrice;
                        ?>
                        <div class="col-md-6">
                            <div class="card h-100 <?php echo $isCurrent ? 'border-warning' : ''; ?> <?php echo !$canAfford ? 'opacity-50' : ''; ?>">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <?php echo htmlspecialchars($sub['name']); ?>
                                        <?php if ($isCurrent): ?>
                                            <span class="badge bg-warning text-dark">Gói hiện tại</span>
                                        <?php endif; ?>
                                    </h5>
                                    <p class="card-text text-muted small"><?php echo htmlspecialchars($sub['description']); ?></p>
                                    <div class="mb-2">
                                        <strong class="text-danger"><?php echo number_format($subPrice, 0, ',', '.'); ?> điểm</strong>
                                    </div>
                                    <?php if ($sub['benefits']): ?>
                                        <small class="text-muted d-block mb-2"><?php echo htmlspecialchars($sub['benefits']); ?></small>
                                    <?php endif; ?>
                                    
                                    <?php if ($isCurrent): ?>
                                        <button class="btn btn-secondary w-100" disabled>Đang sử dụng</button>
                                    <?php elseif ($isHigher): ?>
                                        <button class="btn btn-secondary w-100" disabled>Gói thấp hơn</button>
                                    <?php elseif (!$canAfford): ?>
                                        <button class="btn btn-secondary w-100" disabled>Không đủ điểm</button>
                                    <?php else: ?>
                                        <form method="POST" action="?route=profile/upgradeSubscription" class="d-inline">
                                            <input type="hidden" name="subscription_id" value="<?php echo $sub['id']; ?>">
                                            <button type="submit" class="btn btn-warning w-100" onclick="return confirm('Bạn có chắc muốn nâng cấp lên gói <?php echo htmlspecialchars($sub['name']); ?> với giá <?php echo number_format($subPrice, 0, ',', '.'); ?> điểm?');">
                                                <i class="fas fa-arrow-up me-2"></i> Nâng cấp
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function openUpgradeModal() {
    const modal = new bootstrap.Modal(document.getElementById('upgradeModal'));
    modal.show();
}

// Avatar upload functionality
document.getElementById('avatar-input').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (!file) return;
    
    // Validate file type
    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    if (!allowedTypes.includes(file.type)) {
        alert('Chỉ chấp nhận file ảnh (JPG, PNG, GIF, WEBP)!');
        return;
    }
    
    // Validate file size (5MB)
    if (file.size > 5 * 1024 * 1024) {
        alert('Kích thước file quá lớn! Tối đa 5MB.');
        return;
    }
    
    // Preview image
    const reader = new FileReader();
    reader.onload = function(e) {
        const preview = document.getElementById('avatar-preview');
        const placeholder = document.querySelector('.avatar-placeholder-luxury');
        
        if (preview) {
            preview.src = e.target.result;
        } else if (placeholder) {
            placeholder.outerHTML = '<img src="' + e.target.result + '" alt="Avatar" id="avatar-preview" style="width: 100%; height: 100%; object-fit: cover;">';
        }
    };
    reader.readAsDataURL(file);
    
    // Upload to server
    const formData = new FormData();
    formData.append('avatar', file);
    
    fetch('?route=profile/uploadAvatar', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update avatar preview with new URL
            const preview = document.getElementById('avatar-preview');
            const placeholder = document.querySelector('.avatar-placeholder-luxury');
            const changeAvatarBtn = document.querySelector('.btn-change-avatar span');
            
            if (preview && data.avatar_url) {
                preview.src = data.avatar_url;
            } else if (placeholder && data.avatar_url) {
                placeholder.outerHTML = '<img src="' + data.avatar_url + '" alt="Avatar" id="avatar-preview" style="width: 100%; height: 100%; object-fit: cover;">';
            }
            
            // Update button text if it was "Thêm ảnh đại diện"
            if (changeAvatarBtn && changeAvatarBtn.textContent.includes('Thêm')) {
                changeAvatarBtn.textContent = 'Đổi ảnh đại diện';
            }
            
            // Show success message
            if (typeof showNotification !== 'undefined') {
                showNotification(data.message, 'success');
            } else {
                alert(data.message);
            }
        } else {
            alert(data.message || 'Có lỗi xảy ra khi upload ảnh!');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Có lỗi xảy ra khi upload ảnh!');
    });
});
</script>
