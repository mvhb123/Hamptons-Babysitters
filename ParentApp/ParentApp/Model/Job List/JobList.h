//
//  JobList.h
//  ParentApp
//
//  Created by Sandeep Kumar Singh on 15/06/15.
//  Copyright (c) 2015 Sofmen. All rights reserved.
//

#import <Foundation/Foundation.h>
#import "Children.h"

@interface JobList : NSObject

@property (nonatomic, strong) NSString *actualEndDate;
@property (nonatomic, strong) NSString *actualStartDate;
@property (nonatomic, strong) NSString *addressId;
@property (nonatomic, strong) NSString *clientUserId;
@property (nonatomic, strong) NSString *jobEndDate;
@property (nonatomic, strong) NSString *jobStartDate;
@property (nonatomic, strong) NSString *jobId;
@property (nonatomic, strong) NSString *jobStatus;
@property (nonatomic, strong) NSString *jobAddress;
@property (nonatomic, strong) NSString *jobPostedDate;
@property (nonatomic, strong) NSString *notes;
@property (nonatomic, strong) NSString *sitterFirstName;
@property (nonatomic, strong) NSString *sitterLastName;
@property (nonatomic, strong) NSString *sitterPhone;
@property (nonatomic, strong) NSString *sitterUserId;
@property (nonatomic, strong) NSString *sitterUserName;
@property (nonatomic, strong) NSString *sitterProfilePic;
@property (nonatomic, strong) NSString *rate;
@property (nonatomic, strong) NSString *childCount;
@property (nonatomic, strong) NSString *compleatedDate;
@property (nonatomic, strong) NSString *firstName;
@property (nonatomic, strong) NSString *lastName;
@property (nonatomic, strong) NSString *lastModifiedDate;
@property (nonatomic, strong) NSString *totalPaid;
@property (nonatomic, strong) NSString *totalHours;
@property (nonatomic, strong) NSString *totalAssigned;
@end


