<?php

return [

    // ================== AUTH ==================
    'auth' => [
        'token_required' => 'Token is required',
        'token_invalid' => 'Invalid token',
        'token_expired' => 'Token has expired',
        'account_inactive' => 'Account is not active',
        'role_required' => 'You do not have permission to access this resource',
        'permission_denied' => 'You do not have permission to perform this action',
        'unauthorized' => 'Unauthorized access',
    ],

    // ================== PROFILE ==================
    'profile' => [
        'not_found' => 'Business profile not found',
        'already_exists' => 'You already have a business profile',
        'created' => 'Business profile created successfully',
        'updated' => 'Business profile updated successfully',
        'cannot_be_modified' => 'Profile cannot be modified in this state',
        'required' => 'You must create a business profile first',
        'required_for_rental' => 'You must verify your profile to submit rental requests',
        'pending' => 'Your profile is under review',
        'approved' => 'Profile has been verified',
        'rejected' => 'Profile verification has been rejected',
        'not_pending' => 'Profile is not pending review',
    ],

    // ================== EVENT ==================
    'event' => [
        'not_found' => 'Event not found',
        'created' => 'Event created successfully',
        'updated' => 'Event updated successfully',
        'deleted' => 'Event deleted successfully',
        'has_active_requests' => 'Cannot delete event with active requests',
    ],

    // ================== SPACE ==================
    'space' => [
        'not_found' => 'Space not found',
        'created' => 'Space created successfully',
        'updated' => 'Space updated successfully',
        'deleted' => 'Space deleted successfully',
        'has_active_rentals' => 'Cannot delete space with active rentals',
    ],

    // ================== VISIT REQUEST ==================
    'visit_request' => [
        'not_found' => 'Visit request not found',
        'already_exists' => 'You already have a visit request for this date',
        'created' => 'Visit request created successfully',
        'updated' => 'Visit request updated successfully',
        'cancelled' => 'Visit request cancelled',
        'cannot_be_modified' => 'Request cannot be modified in this state',
        'cannot_be_cancelled' => 'Request cannot be cancelled in this state',
        'date_outside_event' => 'Visit date is outside event period',
        'event_not_available' => 'Event is not available for visits',
        'approved' => 'Visit request approved',
        'rejected' => 'Visit request rejected',
        'not_pending' => 'Request is not pending',
    ],

    // ================== RENTAL REQUEST ==================
    'rental_request' => [
        'not_found' => 'Rental request not found',
        'already_exists' => 'You already have a rental request for this space in this period',
        'created' => 'Rental request created successfully',
        'updated' => 'Rental request updated successfully',
        'cancelled' => 'Rental request cancelled',
        'cannot_be_modified' => 'Request cannot be modified in this state',
        'cannot_be_cancelled' => 'Request cannot be cancelled in this state',
        'dates_outside_event' => 'Rental dates are outside event period',
        'space_not_available' => 'Space is not available for this period',
        'approved' => 'Rental request approved',
        'rejected' => 'Rental request rejected',
        'not_pending' => 'Request is not pending',
        'must_be_approved' => 'Request must be approved',
        'payment_recorded' => 'Payment recorded successfully',
    ],

    // ================== FAVORITE ==================
    'favorite' => [
        'added' => 'Added to favorites',
        'removed' => 'Removed from favorites',
    ],

    // ================== NOTIFICATION ==================
    'notification' => [
        'marked_as_read' => 'Notification marked as read',
        'all_marked_as_read' => 'All notifications marked as read',
        'preferences_updated' => 'Notification preferences updated successfully',
    ],

    // ================== DEVICE ==================
    'device' => [
        'registered' => 'Device registered for push notifications',
        'unregistered' => 'Device unregistered from push notifications',
        'not_found' => 'Device not found',
    ],

    // ================== RATING ==================
    'rating' => [
        'created' => 'Rating submitted successfully',
        'updated' => 'Rating updated successfully',
        'deleted' => 'Rating deleted successfully',
        'approved' => 'Rating approved',
        'rejected' => 'Rating rejected',
        'already_rated' => 'You have already rated this item',
        'not_found' => 'Rating not found',
    ],

    // ================== SUPPORT TICKET ==================
    'support_ticket' => [
        'not_found' => 'Support ticket not found',
        'list_fetched' => 'Support tickets retrieved successfully',
        'fetched' => 'Support ticket retrieved successfully',
        'created' => 'Support ticket created successfully',
        'reply_added' => 'Reply added successfully',
        'assigned' => 'Ticket assigned successfully',
        'resolved' => 'Ticket resolved successfully',
        'closed' => 'Ticket closed',
        'reopened' => 'Ticket reopened',
        'ticket_closed' => 'This ticket is closed and cannot receive replies',
        'already_closed' => 'Ticket is already closed',
    ],

    // ================== RENTAL CONTRACT ==================
    'rental_contract' => [
        'not_found' => 'Rental contract not found',
        'created' => 'Rental contract created successfully',
        'updated' => 'Rental contract updated successfully',
        'approved' => 'Rental contract approved and activated',
        'rejected' => 'Rental contract rejected',
        'terminated' => 'Rental contract terminated',
        'cannot_be_modified' => 'Contract cannot be modified in this state',
        'not_pending' => 'Contract is not pending approval',
        'signed' => 'Contract signed successfully',
    ],

    // ================== INVOICE ==================
    'invoice' => [
        'not_found' => 'Invoice not found',
        'created' => 'Invoice created successfully',
        'updated' => 'Invoice updated successfully',
        'issued' => 'Invoice issued successfully',
        'marked_paid' => 'Invoice marked as paid',
        'cancelled' => 'Invoice cancelled',
        'cannot_be_modified' => 'Invoice cannot be modified in this state',
        'already_paid' => 'Invoice is already paid',
    ],

    // ================== PAGE ==================
    'page' => [
        'not_found' => 'Page not found',
        'created' => 'Page created successfully',
        'updated' => 'Page updated successfully',
        'deleted' => 'Page deleted successfully',
    ],

    // ================== FAQ ==================
    'faq' => [
        'not_found' => 'FAQ not found',
        'created' => 'FAQ created successfully',
        'updated' => 'FAQ updated successfully',
        'deleted' => 'FAQ deleted successfully',
        'helpful_recorded' => 'Thank you for your feedback',
    ],

    // ================== BANNER ==================
    'banner' => [
        'not_found' => 'Banner not found',
        'created' => 'Banner created successfully',
        'updated' => 'Banner updated successfully',
        'deleted' => 'Banner deleted successfully',
    ],

    // ================== SPONSOR ==================
    'sponsor' => [
        'not_found' => 'Sponsor not found',
        'created' => 'Sponsor created successfully',
        'updated' => 'Sponsor updated successfully',
        'deleted' => 'Sponsor deleted successfully',
        'approved' => 'Sponsor approved successfully',
        'activated' => 'Sponsor activated successfully',
        'suspended' => 'Sponsor suspended',
        'deactivated' => 'Sponsor deactivated',
        'already_exists' => 'Sponsor already exists for this event',
        'not_active' => 'Sponsor is not active',
        'has_active_contracts' => 'Cannot delete sponsor with active contracts',
    ],

    // ================== SPONSOR PACKAGE ==================
    'sponsor_package' => [
        'not_found' => 'Sponsor package not found',
        'created' => 'Sponsor package created successfully',
        'updated' => 'Sponsor package updated successfully',
        'deleted' => 'Sponsor package deleted successfully',
        'not_available' => 'This sponsor package is not available',
        'full' => 'This sponsor package has reached maximum sponsors',
        'has_active_contracts' => 'Cannot delete package with active contracts',
    ],

    // ================== SPONSOR CONTRACT ==================
    'sponsor_contract' => [
        'not_found' => 'Sponsor contract not found',
        'created' => 'Sponsor contract created successfully',
        'updated' => 'Sponsor contract updated successfully',
        'approved' => 'Sponsor contract approved and activated',
        'rejected' => 'Sponsor contract rejected',
        'completed' => 'Sponsor contract completed',
        'cancelled' => 'Sponsor contract cancelled',
        'cannot_be_modified' => 'Contract cannot be modified in this state',
        'already_active' => 'Contract is already active',
        'payment_recorded' => 'Payment recorded successfully',
    ],

    // ================== SPONSOR PAYMENT ==================
    'sponsor_payment' => [
        'not_found' => 'Sponsor payment not found',
        'created' => 'Payment schedule created successfully',
        'updated' => 'Payment updated successfully',
        'marked_paid' => 'Payment marked as paid',
        'already_paid' => 'Payment is already paid',
    ],

    // ================== SPONSOR BENEFIT ==================
    'sponsor_benefit' => [
        'not_found' => 'Sponsor benefit not found',
        'created' => 'Benefit added successfully',
        'updated' => 'Benefit updated successfully',
        'delivered' => 'Benefit marked as delivered',
    ],

    // ================== SPONSOR ASSET ==================
    'sponsor_asset' => [
        'not_found' => 'Sponsor asset not found',
        'uploaded' => 'Asset uploaded successfully',
        'updated' => 'Asset updated successfully',
        'deleted' => 'Asset deleted successfully',
        'approved' => 'Asset approved',
        'rejected' => 'Asset rejected',
        'upload_failed' => 'Asset upload failed',
        'limit_reached' => 'Maximum number of assets reached',
    ],

    // ================== VALIDATION ==================
    'validation' => [
        'company_name_required' => 'Company name is required',
        'contact_phone_required' => 'Contact phone is required',
        'business_type_required' => 'Business type is required',
        'must_be_image' => 'File must be an image',
        'file_too_large' => 'File is too large',
        'event_required' => 'Event is required',
        'visit_date_required' => 'Visit date is required',
        'visit_date_future' => 'Visit date must be in the future',
        'visitors_count_required' => 'Visitors count is required',
        'visitors_count_min' => 'Visitors count must be at least 1',
        'visitors_count_max' => 'Visitors count exceeds the maximum allowed',
        'space_required' => 'Space is required',
        'start_date_required' => 'Start date is required',
        'start_date_future' => 'Start date must be in the future',
        'end_date_required' => 'End date is required',
        'end_date_after_start' => 'End date must be after start date',
        'type_required' => 'Type is required',
        'invalid_type' => 'Invalid type',
        'id_required' => 'ID is required',
        'name_required' => 'Name is required',
        'name_ar_required' => 'Arabic name is required',
        'category_required' => 'Category is required',
        'city_required' => 'City is required',
        'address_required' => 'Address is required',
        'location_code_required' => 'Location code is required',
        'location_code_unique' => 'Location code is already in use',
        'area_required' => 'Area is required',
        'price_required' => 'Price is required',
        'rejection_reason_required' => 'Rejection reason is required',
    ],

    // ================== GENERAL ==================
    'resource_not_found' => 'Resource not found',
    'forbidden' => 'This action is not allowed',
    'unauthorized' => 'Unauthorized',
    'validation_failed' => 'Validation failed',
    'server_error' => 'Server error',

    // ================== TRACKING ==================
    'tracking' => [
        'view_recorded' => 'View recorded successfully',
        'action_recorded' => 'Action recorded successfully',
        'invalid_resource_type' => 'Invalid resource type for tracking',
        'invalid_action' => 'Invalid tracking action',
    ],
];
