

const MapaIndex = (function() {
    'use strict';

    // Configuration
    const config = {
        routes: {
            index: '/asesor/mapa'
        },
        selectors: {
            filterForm: '#filterForm',
            searchInput: '#searchInput',
            dateRangeFilter: '#dateRangeFilter',
            statusMapaFilter: '#statusMapaFilter',
            statusAplFilter: '#statusAplFilter',
            applyFilter: '#applyFilter',
            resetFilter: '#resetFilter',
            resetFilterEmpty: '#resetFilterEmpty',
            quickDate: '.quick-date',
            tableBody: '#tableBody',
            paginationInfo: '#paginationInfo',
            paginationLinks: '#paginationLinks',
            paginationContainer: '#paginationContainer',
            tableContainer: '.table-container',
            counterNumber: '.counter-number',
            statsCards: '#statsCards'
        },
        debounceDelay: 500,
        animationDuration: 1500
    };

    // State management
    let state = {
        isLoading: false,
        currentPage: 1,
        searchTimeout: null
    };

    // Initialize module
    function init() {
        initDateRangePicker();
        bindEvents();
        animateCounters();
    }

    // Initialize Date Range Picker
    function initDateRangePicker() {
        const today = moment();
        const threeDaysAgo = moment().subtract(3, 'days');

        $(config.selectors.dateRangeFilter).daterangepicker({
            autoUpdateInput: false,
            locale: {
                format: 'DD/MM/YYYY',
                separator: ' - ',
                applyLabel: 'Terapkan',
                cancelLabel: 'Batal',
                fromLabel: 'Dari',
                toLabel: 'Sampai',
                customRangeLabel: 'Custom',
                daysOfWeek: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
                monthNames: [
                    'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                ],
                firstDay: 1
            },
            ranges: {
                'Hari Ini': [moment(), moment()],
                'Kemarin': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                '3 Hari Terakhir': [moment().subtract(3, 'days'), moment()],
                '7 Hari Terakhir': [moment().subtract(6, 'days'), moment()],
                '30 Hari Terakhir': [moment().subtract(29, 'days'), moment()],
                'Bulan Ini': [moment().startOf('month'), moment().endOf('month')],
                'Bulan Lalu': [
                    moment().subtract(1, 'month').startOf('month'),
                    moment().subtract(1, 'month').endOf('month')
                ]
            },
            startDate: threeDaysAgo,
            endDate: today,
            maxDate: moment()
        });

        // Handle date range events
        $(config.selectors.dateRangeFilter).on('apply.daterangepicker', function(ev, picker) {
            $(this).val(
                picker.startDate.format('DD/MM/YYYY') + ' - ' + 
                picker.endDate.format('DD/MM/YYYY')
            );
        });

        $(config.selectors.dateRangeFilter).on('cancel.daterangepicker', function() {
            $(this).val('');
        });
    }

    // Bind all event handlers
    function bindEvents() {
        // Apply filter button
        $(config.selectors.applyFilter).on('click', function() {
            loadData(1);
        });

        // Quick date filter buttons
        $(config.selectors.quickDate).on('click', handleQuickDate);

        // Reset filter buttons
        $(config.selectors.resetFilter + ', ' + config.selectors.resetFilterEmpty)
            .on('click', handleResetFilter);

        // Select filters with auto-submit
        $(config.selectors.statusMapaFilter + ', ' + config.selectors.statusAplFilter)
            .on('change', function() {
                loadData(1);
            });

        // Search input with debounce
        $(config.selectors.searchInput).on('keyup', handleSearchInput);

        // Pagination click handler (event delegation)
        $(document).on('click', config.selectors.paginationLinks + ' .page-link', handlePagination);

        // Handle browser back/forward buttons
        window.addEventListener('popstate', function() {
            location.reload();
        });
    }

    // Handle quick date selection
    function handleQuickDate() {
        const days = parseInt($(this).data('days'));
        let startDate, endDate;

        if (days === 0) {
            startDate = moment();
            endDate = moment();
        } else {
            startDate = moment().subtract(days - 1, 'days');
            endDate = moment();
        }

        const $dateFilter = $(config.selectors.dateRangeFilter);
        $dateFilter.data('daterangepicker').setStartDate(startDate);
        $dateFilter.data('daterangepicker').setEndDate(endDate);
        $dateFilter.val(
            startDate.format('DD/MM/YYYY') + ' - ' + 
            endDate.format('DD/MM/YYYY')
        );

        $(config.selectors.quickDate).removeClass('active');
        $(this).addClass('active');

        loadData(1);
    }

    // Handle reset filter
    function handleResetFilter() {
        $(config.selectors.filterForm)[0].reset();
        $(config.selectors.dateRangeFilter).val('');
        $(config.selectors.quickDate).removeClass('active');
        
        const baseUrl = window.location.origin + window.location.pathname;
        window.history.pushState({}, '', baseUrl);
        location.reload();
    }

    // Handle search input with debounce
    function handleSearchInput() {
        clearTimeout(state.searchTimeout);
        state.searchTimeout = setTimeout(function() {
            loadData(1);
        }, config.debounceDelay);
    }

    // Handle pagination click
    function handlePagination(e) {
        e.preventDefault();
        const url = $(this).attr('href');
        if (url) {
            const page = new URL(url).searchParams.get('page') || 1;
            loadData(page);
        }
    }

    // Show loading overlay
    function showLoading() {
        if (!state.isLoading) {
            state.isLoading = true;
            $(config.selectors.tableContainer).css('position', 'relative');
            $('<div class="loading-overlay"><div class="loading-spinner"></div></div>')
                .appendTo(config.selectors.tableContainer);
        }
    }

    // Hide loading overlay
    function hideLoading() {
        state.isLoading = false;
        $('.loading-overlay').remove();
    }

    // Load data via AJAX
    function loadData(page = 1) {
        if (state.isLoading) return;

        showLoading();
        state.currentPage = page;

        const formData = getFormData(page);

        $.ajax({
            url: config.routes.index,
            type: 'GET',
            data: formData,
            dataType: 'json',
            success: handleLoadSuccess,
            error: handleLoadError,
            complete: hideLoading
        });
    }

    // Get form data for AJAX request
    function getFormData(page) {
        return {
            search: $(config.selectors.searchInput).val(),
            date_range: $(config.selectors.dateRangeFilter).val(),
            status_mapa: $(config.selectors.statusMapaFilter).val(),
            status_apl: $(config.selectors.statusAplFilter).val(),
            page: page
        };
    }

    // Handle successful data load
    function handleLoadSuccess(response) {
        if (response.success) {
            // Update table body
            $(config.selectors.tableBody).html(response.html);

            // Update pagination
            updatePagination(response.pagination, response.pagination_html);

            // Update stats with animation
            updateStats(response.stats);

            // Update URL without reload
            updateBrowserUrl(getFormData(state.currentPage));

            // Scroll to top of table
            $('html, body').animate({
                scrollTop: $(config.selectors.tableContainer).offset().top - 100
            }, 300);
        }
    }

    // Handle data load error
    function handleLoadError(xhr) {
        console.error('Error loading data:', xhr);
        
        let errorMessage = 'Gagal memuat data. Silakan coba lagi.';
        if (xhr.responseJSON && xhr.responseJSON.message) {
            errorMessage = xhr.responseJSON.message;
        }

        showNotification('error', errorMessage);
    }

    // Update pagination UI
    function updatePagination(pagination, paginationHtml) {
        if (pagination.total > 0) {
            $(config.selectors.paginationInfo).html(
                `Menampilkan ${pagination.from} - ${pagination.to} dari ${pagination.total} data`
            );
            $(config.selectors.paginationLinks).html(paginationHtml);
            $(config.selectors.paginationContainer).show();
        } else {
            $(config.selectors.paginationContainer).hide();
        }
    }

    // Update stats with animation
    function updateStats(stats) {
        const statMapping = {
            total: 0,
            selesai: 1,
            perlu_dibuat: 2,
            draft: 3
        };

        Object.keys(stats).forEach(key => {
            const index = statMapping[key];
            if (index !== undefined) {
                const $element = $(config.selectors.counterNumber).eq(index);
                $element.data('target', stats[key]);
                animateCounter($element);
            }
        });
    }

    // Animate counter number
    function animateCounter($element) {
        const target = parseInt($element.data('target'));
        
        $({count: 0}).animate({count: target}, {
            duration: config.animationDuration,
            easing: 'swing',
            step: function() {
                $element.text(Math.floor(this.count));
            },
            complete: function() {
                $element.text(target);
            }
        });
    }

    // Animate all counters on page load
    function animateCounters() {
        $(config.selectors.counterNumber).each(function() {
            animateCounter($(this));
        });
    }

    // Update browser URL without reload
    function updateBrowserUrl(formData) {
        const url = new URL(window.location);
        
        Object.keys(formData).forEach(key => {
            if (formData[key] && key !== 'page') {
                url.searchParams.set(key, formData[key]);
            } else if (key !== 'page') {
                url.searchParams.delete(key);
            }
        });

        if (formData.page > 1) {
            url.searchParams.set('page', formData.page);
        } else {
            url.searchParams.delete('page');
        }

        window.history.pushState({}, '', url);
    }

    // Show notification (requires notification library or custom implementation)
    function showNotification(type, message) {
        // Using simple alert for now, can be replaced with toast notification
        if (type === 'error') {
            alert(message);
        } else {
            console.log(type + ': ' + message);
        }
    }

    // Export refresh function for external use
    function refresh() {
        loadData(state.currentPage);
    }

    // Export current filters
    function getCurrentFilters() {
        return getFormData(state.currentPage);
    }

    // Public API
    return {
        init: init,
        loadData: loadData,
        refresh: refresh,
        getCurrentFilters: getCurrentFilters
    };

})();

// Initialize on document ready
$(document).ready(function() {
    MapaIndex.init();
});

// Make available globally if needed
window.MapaIndex = MapaIndex;