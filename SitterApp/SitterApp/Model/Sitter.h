//
//  Sitter.h
//  SitterApp
//
//  Created by Vikram gour on 17/04/15.
//  Copyright (c) 2015 Sofmen. All rights reserved.
//

#import <Foundation/Foundation.h>

@interface Sitter : NSObject
@property(nonatomic,strong)NSURL *sitterProfileImageUrl;
@property(nonatomic,strong)NSURL *sitterProfileOriginalImageUrl;
@property(nonatomic,strong)NSString *sitterName;
@property(nonatomic,strong)NSString *sitterFirstName;
@property(nonatomic,strong)NSString *sitterLastName;
@property(nonatomic,assign)NSInteger *sitterAge;
@property(nonatomic,strong)NSString *sitterGender;
@property(nonatomic,strong)NSString *sitterAboutMe;
@property(nonatomic,strong)NSString *sitterPhone1;//Phone1
@property(nonatomic,strong)NSString *sitterPhone2; //Phone2
@property(nonatomic,strong)NSString *sitterWorkPhone;
@property(nonatomic,strong)NSString *sitterEmail;
@property(nonatomic,strong)NSString *sitterId;
@property(nonatomic,strong)NSString *sitterStatus;
@property(nonatomic,strong)NSString *str_TimeZone;
@property(nonatomic,strong)NSString *str_TokenData;
@property(nonatomic,strong)NSString *str_OtherPreferences;
@property(nonatomic,strong)NSString *appNotificationSetting;
@property(nonatomic,strong)NSMutableDictionary *sitterAdditionalIbfo;
@property(nonatomic,strong)NSMutableArray *array_Certificates;
@property(nonatomic,strong)NSMutableArray *array_Area;
@property(nonatomic,strong)NSMutableArray *array_Child_preferences;
@property(nonatomic,strong)NSMutableArray *array_Other_preferences;
@property(nonatomic,strong)NSMutableArray *array_Language;

@end
