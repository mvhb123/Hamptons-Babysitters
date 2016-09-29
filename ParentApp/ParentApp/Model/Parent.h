//
//  Parent.h
//  ParentApp
//
//  Created by Sandeep Kumar Singh on 29/05/15.
//  Copyright (c) 2015 Sofmen. All rights reserved.
//

#import <Foundation/Foundation.h>

@interface Parent : NSObject
@property(nonatomic,strong)NSString *parentName;
@property(nonatomic,strong)NSString *parentFirstName;
@property(nonatomic,strong)NSString *parentLastName;
@property(nonatomic,strong)NSString *parentChildCountName;
@property(nonatomic,strong)NSString *parentUserId;
@property(nonatomic,strong)NSString *parentUserName;
@property(nonatomic,strong)NSString *parentPhone;
@property(nonatomic,strong)NSString *parentLocalPhone;
@property(nonatomic,strong)NSString *parentRelationship;
@property(nonatomic,strong)NSString *parentEmergencyContactName;
@property(nonatomic,strong)NSString *parentEmergencyPhone;
@property(nonatomic,strong)NSString *parentEmergencyRelation;
@property(nonatomic,strong)NSURL *parentThumbImage;
@property(nonatomic,strong)NSURL *parentOriginalImage;
@property(nonatomic,strong)NSMutableDictionary *dict_parentLocalAddress;
@property(nonatomic,strong)NSURL *parentMainImage;
@property(nonatomic,strong)NSString *parentAvailable_credits;
@property(nonatomic,strong)NSString *parentSitterFees;
@property(nonatomic,strong)NSString *parentGurdianName;
@property(nonatomic,strong)NSString *parentGurdianPhone1;
@property(nonatomic,strong)NSString *parentGurdianPhone2;
@property(nonatomic,strong)NSString *parentGurdianRelationship;
@property(nonatomic,strong)NSString *parentTimeZone;
@property(nonatomic,strong)NSString *tokenData;
@property(nonatomic,strong)NSString *HotelName;
@property(nonatomic,strong)NSString *AddressID;
@property(nonatomic,strong)NSString *CrossStreet;
@property(nonatomic,strong)NSString *StreetAddress;
@property(nonatomic,strong)NSString *City;
@property(nonatomic,strong)NSString *State;
@property(nonatomic,strong)NSString *stateID;
@property(nonatomic,strong)NSString *zipCode;
@property(nonatomic,strong)NSString *addressType;
@property(nonatomic,strong)NSString *authrizedPaymentProfileId;
@property(nonatomic,strong)NSString *timeZone;
@property(nonatomic,strong)NSString *profileStatus;


@end
