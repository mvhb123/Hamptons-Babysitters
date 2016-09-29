//
//  APIURL.h
//  SitterApp
//
//  Created by Vikram gour on 01/04/15.
//  Copyright (c) 2015 Sofmen. All rights reserved.
//

#ifndef Boating_App_APIURL_h
#define Boating_App_APIURL_h

#pragma mark- URLS for API calling
//#define kBASE_URL @"http://projects.sofmen.com/apibabysitter" // Dev
#define kBASE_URL @"http://hamptonsbabysitters.com" //Production
//#define kBASE_URL @"http://192.168.50.82/apibabysitter3" // Local Server
#define kAPI_KeyValue @"babysitter@123"

#define kLOGIN_API  [NSString stringWithFormat:@"%@/api/v1/parent/user/parentLogin", kBASE_URL]
#define kFORGOT_PASSWORD_API  [NSString stringWithFormat:@"%@/api/v1/forgetPassword", kBASE_URL]
#define kRegistration_ParentDetail_API [NSString stringWithFormat:@"%@/api/v1/parent/user/registration", kBASE_URL]
#define kStateListAPI      [NSString stringWithFormat:@"%@/api/v1/common/get_state_list", kBASE_URL]
#define kChildrenListApi   [NSString stringWithFormat:@"%@/api/v1/parent/user/getKids", kBASE_URL]
#define kAddChildApi       [NSString stringWithFormat:@"%@/api/v1/parent/user/addeditkid", kBASE_URL]
#define kUpdateUserProfile [NSString stringWithFormat:@"%@/api/v1/parent/user/edit_profile", kBASE_URL]
#define kJobsTypeRequestApi [NSString stringWithFormat:@"%@/api/v1/parent/job/joblist", kBASE_URL]
#define kCancleJobApi [NSString stringWithFormat:@"%@/api/v1/parent/job/cancelJob", kBASE_URL]
#define kSitterRequirementAPI [NSString stringWithFormat:@"%@/api/v1/parent/user/get_job_prefer_list", kBASE_URL]
#define kLogOutAPI [NSString stringWithFormat:@"%@/api/v1/logout", kBASE_URL]
#define kPackageList [NSString stringWithFormat:@"%@/api/v1/parent/package", kBASE_URL]
#define kBookingFeeJobSummaryAPI [NSString stringWithFormat:@"%@/api/v1/parent/job/calculateBookingFee", kBASE_URL]
#define kAddJobAPI [NSString stringWithFormat:@"%@/api/v1/parent/job/addeditjob", kBASE_URL]
#define kJobDetailAPI [NSString stringWithFormat:@"%@/api/v1/parent/job/jobdetail", kBASE_URL]
#define kChangePasswordAPI [NSString stringWithFormat:@"%@/api/v1/changePassword", kBASE_URL]
#define kSitterDetailAPI [NSString stringWithFormat:@"%@/api/v1/parent/user/sitterProfile", kBASE_URL]
#define kGetSavedCardAPI [NSString stringWithFormat:@"%@/api/v1/parent/getCards", kBASE_URL]
#define kAddEditCardDetail [NSString stringWithFormat:@"%@/api/v1/parent/package/addEditcard", kBASE_URL]
#define kBuyCreditsAPI [NSString stringWithFormat:@"%@/api/v1/parent/package/buyPackage", kBASE_URL]
#define kUpdateAppNotification_API [NSString stringWithFormat:@"%@/common/update_notification_setting",kBASE_URL]
#define kUpdateBadgeCount_API [NSString stringWithFormat:@"%@/api/v1/common/decrease_badge_count",kBASE_URL]
#define kGetRelationShipListApi       [NSString stringWithFormat:@"%@/api/v1/common/get_relation_to_child", kBASE_URL]
#define kGetSpecialNeedListApi       [NSString stringWithFormat:@"%@/api/v1/common/get_special_needs", kBASE_URL]
#define kResetNotification_API [NSString stringWithFormat:@"%@/api/v1/common/readallnoification",kBASE_URL]

#define kAboutUrl [NSString stringWithFormat:@"%@/api/v1/about",kBASE_URL]
#define kContactUsUrl [NSString stringWithFormat:@"%@/api/v1/contact",kBASE_URL]
#define kHowThisWorksUrl [NSString stringWithFormat:@"%@/api/v1/how_it_work",kBASE_URL]

#endif
