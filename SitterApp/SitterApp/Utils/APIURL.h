//
//  APIURL.h
//  SitterApp
//
//  Created by Vikram gour on 01/04/15.
//  Copyright (c) 2015 Sofmen. All rights reserved.
//

#ifndef Sitter_App_APIURL_h
#define Sitter_App_APIURL_h

#pragma mark- URLS for API calling

//#define kBASE_URL @"http://projects.sofmen.com/apibabysitter/api/v1" //Dev server
#define kBASE_URL @"http://hamptonsbabysitters.com/api/v1" //Production server
//#define kBASE_URL @"http://192.168.50.82/apibabysitter1/api/v1" // Local machine
#define kAPI_KeyValue @"babysitter@123"
//#define kSitterRegistrationUrl @"http://projects.sofmen.com/webbabysitter/register"  //Dev server
#define kSitterRegistrationUrl @"http://www.hamptonsbabysitters.com/a/register"// Production

//#define kDefaultImageUrl @"http://projects.sofmen.com/webbabysitter/uploads/noimage.jpg"  //Dev
#define kDefaultImageUrl @"http://www.hamptonsbabysitters.com/a/uploads/noimage.jpg"  //Production




#define kLOGIN_API [NSString stringWithFormat:@"%@/sitter/user/sitterLogin",kBASE_URL]
#define kFORGOT_PASSWORD_API [NSString stringWithFormat:@"%@/forgetPassword",kBASE_URL]
#define kGetAdditionalInfoList_API [NSString stringWithFormat:@"%@/sitter/user/get_sitter_prefer_list",kBASE_URL]
#define kUpdateProfile_API [NSString stringWithFormat:@"%@/sitter/user/edit_profile",kBASE_URL]
#define kJobList_API [NSString stringWithFormat:@"%@/sitter/job/jobList",kBASE_URL]
#define kLogOut_API [NSString stringWithFormat:@"%@/logout",kBASE_URL]
#define kChangePassword_API [NSString stringWithFormat:@"%@/changePassword",kBASE_URL]
#define kUpdateAppNotification_API [NSString stringWithFormat:@"%@/common/update_notification_setting",kBASE_URL]
#define kCancelJob_API [NSString stringWithFormat:@"%@/sitter/job/canceljob",kBASE_URL]
#define kAcceptJob_API [NSString stringWithFormat:@"%@/sitter/job/confirmjob",kBASE_URL]
#define kCompleteJob_API [NSString stringWithFormat:@"%@/sitter/job/complete_job",kBASE_URL]
#define kJobDetail_API [NSString stringWithFormat:@"%@/sitter/job/jobdetail",kBASE_URL]
#define kUpdateBadgeCount_API [NSString stringWithFormat:@"%@/common/decrease_badge_count",kBASE_URL]
#define kGetRelationShipListApi       [NSString stringWithFormat:@"%@/common/get_relation_to_child", kBASE_URL]
#define kGetSpecialNeedListApi       [NSString stringWithFormat:@"%@/common/get_special_needs", kBASE_URL]
#define kAddChildApi       [NSString stringWithFormat:@"%@/parent/user/addeditkid", kBASE_URL]
#define kUpdateSitterStatus_API [NSString stringWithFormat:@"%@/sitter/user/updateStatus",kBASE_URL]
#define kUrlForOurExpectation [NSString stringWithFormat:@"%@/about", kBASE_URL]//@"http://projects.sofmen.com/apibabysitter/api/v1/about"
#define kUrlForContactAdmin [NSString stringWithFormat:@"%@/contact", kBASE_URL]//@"http://projects.sofmen.com/apibabysitter/api/v1/contact"

#define kResetNotification_API [NSString stringWithFormat:@"%@/common/readallnoification",kBASE_URL]
#endif

