<script>
    document.addEventListener("DOMContentLoaded", () => {
        // Collapse all navigation groups except the one with the active item
        const sidebar = Alpine.store('sidebar');

        // Make sure Alpine and sidebar are available
        if (!sidebar || !sidebar.collapsedGroups) return;

        const activeGroup = document.querySelector('[data-active-group]');
        const activeGroupName = activeGroup?.getAttribute('data-active-group');

        sidebar.groups.forEach(group => {
            if (group.name !== activeGroupName && !sidebar.collapsedGroups.includes(group.name)) {
                sidebar.toggleCollapsedGroup(group.name);
            } else if (group.name === activeGroupName && sidebar.collapsedGroups.includes(group.name)) {
                sidebar.toggleCollapsedGroup(group.name); // Make sure it's open
            }
        });
    });
</script>
