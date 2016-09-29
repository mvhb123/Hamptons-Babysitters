//
//  Sitter.h
//  ParentApp
//
//  Created by Sandeep Kumar Singh on 16/06/15.
//  Copyright (c) 2015 Sofmen. All rights reserved.
//

#import <Foundation/Foundation.h>

@interface Sitter : NSObject

@property(nonatomic,copy)NSString *sitterName;
@property(nonatomic,copy)NSString *sitterFirstName;
@property(nonatomic,copy)NSString *sitterLastName;
@property(nonatomic,assign)NSInteger *sitterAge;
@property(nonatomic,copy)NSString *sitterGender;
@property(nonatomic,copy)NSURL *sitterProfileImageUrl;
@property(nonatomic,copy)NSString *sitterAboutMe;
@property(nonatomic,copy)NSString *sitterPhone1;//Phone1
@property(nonatomic,copy)NSString *sitterPhone2; //Phone2
@property(nonatomic,copy)NSString *sitterWorkPhone;
@property(nonatomic,copy)NSString *sitterEmail;
@property(nonatomic,copy)NSString *sitterId;
@property(nonatomic,copy)NSString *str_TimeZone;
@property(nonatomic,copy)NSMutableDictionary *sitterAdditionalIbfo;
@property(nonatomic,copy) NSString *str_TokenData;
@property(nonatomic,copy)NSMutableArray *array_Certificates;
@property(nonatomic,copy)NSMutableArray *array_Area;
@property(nonatomic,copy)NSMutableArray *array_Child_preferences;
@property(nonatomic,copy)NSMutableArray *array_Other_preferences;
@property(nonatomic,copy)NSMutableArray *array_Language;
@property(nonatomic,copy)NSString *appNotificationSetting;


@end
