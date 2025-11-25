<nav class="navbar">
    <div class="nav-left">
        <a href="index.php" class="logo">
            <div class="logo-icon"></div>
            Ticketly
        </a>
    </div>

    <div class="nav-right">

        <?php if (isset($_SESSION['user_id'])): ?>
            <span style="color: #374151; font-weight: 500;">Hi,
                <?php echo explode(' ', $_SESSION['user_name'])[0]; ?></span>
        <?php else: ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="my_tickets.php" style="color: #374151; font-weight: 500; text-decoration: none; display: flex; align-items: center; gap: 6px; padding: 8px 12px; border-radius: 8px; transition: background 0.2s;" 
               onmouseover="this.style.background='#F3F4F6'" 
               onmouseout="this.style.background='transparent'">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                    <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                </svg>
                My Tickets
            </a>
        <?php endif; ?>

        <?php if (isset($_SESSION['user_id'])): ?>
            <div class="user-avatar">
                <div id="userMenu">
                    <a href="logout.php"
                        style="display: block; padding: 10px; color: #374151; text-decoration: none; border-radius: 6px; transition: background 0.2s;"
                        onmouseover="this.style.background='#F3F4F6'"
                        onmouseout="this.style.background='transparent'">Logout</a>
                </div>
            </div>
        <?php else: ?>
            <a href="signup.php" class="btn-signup">Sign Up</a>
            <a href="login.php" class="btn-login">Log In</a>
        <?php endif; ?>
    </div>
</nav>